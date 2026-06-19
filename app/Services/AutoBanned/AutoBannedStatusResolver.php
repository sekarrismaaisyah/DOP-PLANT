<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedBanStatus;
use App\Enums\AutoBannedHsctSyncStatus;
use App\Enums\AutoBannedSystemStatus;
use App\Enums\AutoBannedTreatmentStatus;
use App\Enums\AutoBannedUnbanStatus;
use App\Enums\AutoBannedVerificationStatus;
use App\Models\AutoBannedStatusSnapshot;
use App\Models\AutoBannedUnbanRequest;
use App\Support\AutoBanned\AutoBannedSchema;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class AutoBannedStatusResolver
{
    public function __construct(
        private readonly AutoBannedSlaCalculator $slaCalculator,
    ) {}

    public function syncWorkflowFromUnbanRequests(AutoBannedStatusSnapshot $snapshot): void
    {
        if ($snapshot->system_status !== AutoBannedSystemStatus::NotPassed) {
            $snapshot->treatment_status = AutoBannedTreatmentStatus::None;
            $snapshot->verification_status = AutoBannedVerificationStatus::None;
            $snapshot->hsct_sync_status = AutoBannedHsctSyncStatus::NotRequired;

            if ($snapshot->ban_status !== AutoBannedBanStatus::ClosedUnbanned) {
                $snapshot->ban_status = AutoBannedBanStatus::CloseBanned;
            }

            return;
        }

        $now = now()->timezone(config('app.timezone'));

        if (! AutoBannedSchema::hasUnbanRequestsTable()) {
            $this->applyTreatmentWithoutUnbanRequest($snapshot, $now);

            return;
        }

        try {
            $unbanRequest = AutoBannedUnbanRequest::query()
                ->where('sid', $snapshot->sid)
                ->when($snapshot->week !== '', fn ($q) => $q->where('week', $snapshot->week))
                ->when($snapshot->iso_year !== '', fn ($q) => $q->where('iso_year', $snapshot->iso_year))
                ->orderByDesc('created_at')
                ->first();
        } catch (QueryException $exception) {
            if (AutoBannedSchema::isMissingTableException($exception)) {
                $this->applyTreatmentWithoutUnbanRequest($snapshot, $now);

                return;
            }

            throw $exception;
        }

        if ($unbanRequest === null) {
            $this->applyTreatmentWithoutUnbanRequest($snapshot, $now);

            return;
        }

        if ($snapshot->treatment_submitted_at === null) {
            $snapshot->treatment_submitted_at = $unbanRequest->created_at;
        }

        $snapshot->treatment_status = AutoBannedTreatmentStatus::Submitted;

        if ($unbanRequest->status === AutoBannedUnbanStatus::Pending) {
            $verificationDeadline = $unbanRequest->created_at
                ->copy()
                ->addDays(AutoBannedSlaCalculator::VERIFICATION_DEADLINE_DAYS);
            $snapshot->verification_status = $now->greaterThan($verificationDeadline)
                ? AutoBannedVerificationStatus::Overdue
                : AutoBannedVerificationStatus::NeedVerifikasi;
            $snapshot->ban_status = AutoBannedBanStatus::OnTreatmentBanned;

            return;
        }

        if ($unbanRequest->status === AutoBannedUnbanStatus::Rejected) {
            $snapshot->verification_status = AutoBannedVerificationStatus::None;
            $snapshot->treatment_status = AutoBannedTreatmentStatus::NeedSubmit;
            $snapshot->treatment_submitted_at = null;

            return;
        }

        if ($snapshot->verification_done_at === null) {
            $snapshot->verification_done_at = $unbanRequest->reviewed_at ?? $unbanRequest->updated_at;
        }

        $verificationDeadline = ($unbanRequest->reviewed_at ?? $unbanRequest->created_at)
            ->copy()
            ->addDays(AutoBannedSlaCalculator::VERIFICATION_DEADLINE_DAYS);
        $snapshot->verification_status = $now->greaterThan($verificationDeadline)
            ? AutoBannedVerificationStatus::DoneOverdue
            : AutoBannedVerificationStatus::Done;

        if ($snapshot->unban_opened_at === null) {
            $snapshot->unban_opened_at = $snapshot->verification_done_at;
        }

        if ($snapshot->hsct_sync_status === AutoBannedHsctSyncStatus::Confirmed
            && $snapshot->verification_status === AutoBannedVerificationStatus::Done) {
            $snapshot->ban_status = AutoBannedBanStatus::ClosedUnbanned;
            $snapshot->unban_closed_at ??= $snapshot->hsct_confirmed_at ?? $now;
        } elseif ($snapshot->hsct_sync_status === AutoBannedHsctSyncStatus::Confirmed) {
            $snapshot->ban_status = AutoBannedBanStatus::CloseBanned;
        }
    }

    private function applyTreatmentWithoutUnbanRequest(AutoBannedStatusSnapshot $snapshot, CarbonInterface $now): void
    {
        $treatmentDeadline = ($snapshot->banned_detected_at ?? $snapshot->first_seen_at)
            ->copy()
            ->addDays(AutoBannedSlaCalculator::TREATMENT_DEADLINE_DAYS);
        $snapshot->treatment_status = $now->greaterThan($treatmentDeadline)
            ? AutoBannedTreatmentStatus::Overdue
            : AutoBannedTreatmentStatus::NeedSubmit;
        $snapshot->verification_status = AutoBannedVerificationStatus::None;
    }

    /**
     * @return array<string, mixed>
     */
    public function toMonitoringRow(AutoBannedStatusSnapshot $snapshot, CarbonInterface $now): array
    {
        $this->syncWorkflowFromUnbanRequests($snapshot);
        $sla = $this->slaCalculator->resolve($snapshot, $now);

        if ($snapshot->isDirty()) {
            $snapshot->ban_status = $sla['banStatus'];
            $snapshot->save();
        } elseif ($snapshot->ban_status !== $sla['banStatus']) {
            $snapshot->ban_status = $sla['banStatus'];
            $snapshot->save();
        }

        return [
            'id' => $snapshot->id,
            'sid' => $snapshot->sid,
            'karyawan' => $snapshot->karyawan,
            'perusahaan' => $snapshot->perusahaan,
            'site' => $snapshot->site_dedicated,
            'week' => $snapshot->week,
            'year' => $snapshot->iso_year,
            'reason' => $snapshot->banned_reason,
            'systemStatus' => $snapshot->system_status,
            'banStatus' => $sla['banStatus'],
            'treatmentStatus' => $snapshot->treatment_status,
            'verificationStatus' => $snapshot->verification_status,
            'hsctSyncStatus' => $snapshot->hsct_sync_status,
            'followUpLabel' => $sla['followUpLabel'],
            'slaBannedLabel' => $sla['slaBannedLabel'],
            'slaBannedDetail' => $sla['slaBannedDetail'],
            'slaBannedTone' => $sla['slaBannedTone'],
            'slaUnbannedLabel' => $sla['slaUnbannedLabel'],
            'slaUnbannedDetail' => $sla['slaUnbannedDetail'],
            'slaUnbannedTone' => $sla['slaUnbannedTone'],
            'remainingBannedLabel' => $sla['remainingBannedLabel'],
            'remainingBannedDetail' => $sla['remainingBannedDetail'],
            'bannedDetectedAt' => $snapshot->banned_detected_at?->format('d M Y H:i'),
            'statusChangedAt' => $snapshot->status_changed_at?->format('d M Y H:i'),
            'lastSeenAt' => $snapshot->last_seen_at?->format('d M Y H:i'),
            'hsctSentAt' => $snapshot->hsct_sent_at?->format('d M Y H:i'),
            'hsctConfirmedAt' => $snapshot->hsct_confirmed_at?->format('d M Y H:i'),
            'scrapStatusRaw' => $snapshot->scrap_status_raw,
        ];
    }

    /**
     * @param  array<string, mixed>  $scrapRow
     * @return array<string, mixed>
     */
    public function fromScrapRow(array $scrapRow): array
    {
        $normalizer = app(AutoBannedStatusNormalizer::class);
        $systemStatus = $normalizer->resolveSystemStatus($scrapRow['status'] ?? '');

        return [
            'id' => $scrapRow['id'] ?? null,
            'sid' => $scrapRow['sid'] ?? '',
            'karyawan' => $scrapRow['karyawan'] ?? '',
            'systemStatus' => $systemStatus,
            'followUpLabel' => $systemStatus === AutoBannedSystemStatus::NotPassed
                ? 'Banned (dari scraping)'
                : 'Registered (dari scraping)',
            'slaBannedLabel' => $systemStatus === AutoBannedSystemStatus::NotPassed ? 'Counting open…' : '—',
            'slaBannedDetail' => '—',
            'slaBannedTone' => 'wait',
            'banStatus' => $systemStatus === AutoBannedSystemStatus::NotPassed
                ? AutoBannedBanStatus::OpenBanned
                : AutoBannedBanStatus::CloseBanned,
            'remainingBannedLabel' => '—',
            'remainingBannedDetail' => '—',
            'treatmentStatus' => $systemStatus === AutoBannedSystemStatus::NotPassed
                ? AutoBannedTreatmentStatus::NeedSubmit
                : AutoBannedTreatmentStatus::None,
            'verificationStatus' => AutoBannedVerificationStatus::None,
            'slaUnbannedLabel' => '—',
            'slaUnbannedDetail' => '—',
            'slaUnbannedTone' => 'muted',
        ];
    }

    /**
     * @param  Collection<int, AutoBannedStatusSnapshot>  $snapshots
     * @return array{
     *     totalDetected: int,
     *     hsctPending: int,
     *     hsctSent: int,
     *     hsctConfirmed: int,
     *     openBanned: int,
     *     overdueBanned: int,
     *     onTreatment: int,
     *     openUnbanned: int,
     *     closedUnbanned: int
     * }
     */
    public function buildSyncStats(Collection $snapshots): array
    {
        $notPassed = $snapshots->where('system_status', AutoBannedSystemStatus::NotPassed);

        return [
            'totalDetected' => $notPassed->count(),
            'hsctPending' => $notPassed->where('hsct_sync_status', AutoBannedHsctSyncStatus::Pending)->count(),
            'hsctSent' => $notPassed->where('hsct_sync_status', AutoBannedHsctSyncStatus::Sent)->count(),
            'hsctConfirmed' => $notPassed->where('hsct_sync_status', AutoBannedHsctSyncStatus::Confirmed)->count(),
            'openBanned' => $notPassed->where('ban_status', AutoBannedBanStatus::OpenBanned)->count(),
            'overdueBanned' => $notPassed->where('ban_status', AutoBannedBanStatus::OverdueBanned)->count(),
            'onTreatment' => $notPassed->where('ban_status', AutoBannedBanStatus::OnTreatmentBanned)->count(),
            'openUnbanned' => $notPassed->where('ban_status', AutoBannedBanStatus::OpenUnbanned)->count(),
            'closedUnbanned' => $notPassed->where('ban_status', AutoBannedBanStatus::ClosedUnbanned)->count(),
        ];
    }
}
