<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedBanStatus;
use App\Enums\AutoBannedHsctSyncStatus;
use App\Enums\AutoBannedStatusChangeType;
use App\Enums\AutoBannedSystemStatus;
use App\Models\AutoBannedPollLog;
use App\Models\AutoBannedStatusChange;
use App\Models\AutoBannedStatusSnapshot;
use App\Models\ScrAutoBannedTbcSap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AutoBannedScrapPollService
{
    private const SCR_TABLE = 'scr_auto_banned_tbc_sap';

    public function __construct(
        private readonly AutoBannedStatusNormalizer $normalizer,
        private readonly AutoBannedStatusResolver $statusResolver,
    ) {}

    public function scrTableAvailable(): bool
    {
        return Schema::hasTable(self::SCR_TABLE);
    }

    public function snapshotsTableAvailable(): bool
    {
        return Schema::hasTable('auto_banned_status_snapshots');
    }

    /**
     * @return array{rows_processed: int, new_snapshots: int, status_changes: int, poll_log_id: ?int}
     */
    public function poll(?string $week = null, ?string $year = null): array
    {
        if (! $this->scrTableAvailable() || ! $this->snapshotsTableAvailable()) {
            return [
                'rows_processed' => 0,
                'new_snapshots' => 0,
                'status_changes' => 0,
                'poll_log_id' => null,
            ];
        }

        $pollLog = AutoBannedPollLog::query()->create([
            'poll_started_at' => now(),
            'status' => 'running',
        ]);

        $rowsProcessed = 0;
        $newSnapshots = 0;
        $statusChanges = 0;

        try {
            $query = ScrAutoBannedTbcSap::query()
                ->whereNotNull('SID')
                ->where('SID', '!=', '');

            if ($week !== null && $week !== '') {
                $query->whereRaw('UPPER(TRIM(CAST(Week AS CHAR))) = ?', [$this->normalizer->normalizeWeek($week)]);
            }

            if ($year !== null && $year !== '') {
                $query->whereRaw('TRIM(CAST(ISO_Year AS CHAR)) = ?', [trim($year)]);
            }

            $query->orderBy('id')->chunkById(200, function ($rows) use (
                &$rowsProcessed,
                &$newSnapshots,
                &$statusChanges,
            ): void {
                foreach ($rows as $scrRow) {
                    $result = $this->processScrapRow($scrRow);
                    $rowsProcessed++;

                    if ($result['is_new']) {
                        $newSnapshots++;
                    }

                    if ($result['has_change']) {
                        $statusChanges++;
                    }
                }
            });

            $pollLog->update([
                'rows_processed' => $rowsProcessed,
                'new_snapshots' => $newSnapshots,
                'status_changes' => $statusChanges,
                'poll_finished_at' => now(),
                'status' => 'completed',
            ]);
        } catch (\Throwable $exception) {
            $pollLog->update([
                'rows_processed' => $rowsProcessed,
                'new_snapshots' => $newSnapshots,
                'status_changes' => $statusChanges,
                'poll_finished_at' => now(),
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return [
            'rows_processed' => $rowsProcessed,
            'new_snapshots' => $newSnapshots,
            'status_changes' => $statusChanges,
            'poll_log_id' => $pollLog->id,
        ];
    }

    /**
     * @return array{is_new: bool, has_change: bool}
     */
    private function processScrapRow(ScrAutoBannedTbcSap $scrRow): array
    {
        $identity = $this->normalizer->resolveIdentity($scrRow);
        if ($identity['sid'] === '' || $identity['week'] === '' || $identity['iso_year'] === '') {
            return ['is_new' => false, 'has_change' => false];
        }

        $newSystemStatus = $this->normalizer->resolveSystemStatus($scrRow->Status_Banned_SID_SAP ?? '');
        $scrapStatusRaw = trim((string) ($scrRow->Status_Banned_SID_SAP ?? ''));
        $now = now()->timezone(config('app.timezone'));

        return DB::transaction(function () use ($scrRow, $identity, $newSystemStatus, $scrapStatusRaw, $now): array {
            $snapshot = AutoBannedStatusSnapshot::query()
                ->where('sid', $identity['sid'])
                ->where('week', $identity['week'])
                ->where('iso_year', $identity['iso_year'])
                ->lockForUpdate()
                ->first();

            $isNew = $snapshot === null;
            $hasChange = false;

            if ($isNew) {
                $snapshot = new AutoBannedStatusSnapshot([
                    'sid' => $identity['sid'],
                    'week' => $identity['week'],
                    'iso_year' => $identity['iso_year'],
                    'first_seen_at' => $now,
                    'status_changed_at' => $now,
                ]);

                $changeType = AutoBannedStatusChangeType::Initial;
                $fromStatus = null;
                $hasChange = true;
            } else {
                $fromStatus = $snapshot->system_status->value;
                $changeType = null;

                if ($snapshot->system_status !== $newSystemStatus) {
                    $hasChange = true;
                    $changeType = $newSystemStatus === AutoBannedSystemStatus::NotPassed
                        ? AutoBannedStatusChangeType::PassToNotPass
                        : ($snapshot->system_status === AutoBannedSystemStatus::NotPassed
                            ? AutoBannedStatusChangeType::NotPassToPass
                            : AutoBannedStatusChangeType::StatusUpdate);
                    $snapshot->status_changed_at = $now;
                }
            }

            $snapshot->karyawan = trim((string) ($scrRow->Karyawan ?? ''));
            $snapshot->perusahaan = trim((string) ($scrRow->Perusahaan ?? ''));
            $snapshot->site_dedicated = trim((string) ($scrRow->Site_Dedicated ?? ''));
            $snapshot->banned_reason = trim((string) ($scrRow->Banned_SID_Reason ?? ''));
            $snapshot->system_status = $newSystemStatus;
            $snapshot->scrap_status_raw = $scrapStatusRaw;
            $snapshot->scr_row_id = $scrRow->id;
            $snapshot->scr_scraped_at = $scrRow->scraped_at;
            $snapshot->last_seen_at = $now;

            if ($newSystemStatus === AutoBannedSystemStatus::NotPassed) {
                if ($snapshot->banned_detected_at === null || ($hasChange && $changeType === AutoBannedStatusChangeType::PassToNotPass)) {
                    $snapshot->banned_detected_at = $now;
                    $snapshot->ban_status = AutoBannedBanStatus::OpenBanned;
                    $snapshot->hsct_sync_status = AutoBannedHsctSyncStatus::Pending;
                }
            } else {
                $snapshot->hsct_sync_status = AutoBannedHsctSyncStatus::NotRequired;
                if ($hasChange && $changeType === AutoBannedStatusChangeType::NotPassToPass) {
                    $snapshot->ban_status = AutoBannedBanStatus::ClosedUnbanned;
                    $snapshot->unban_closed_at = $now;
                }
            }

            $snapshot->save();
            $this->statusResolver->syncWorkflowFromUnbanRequests($snapshot);
            $snapshot->save();

            if ($hasChange && $changeType !== null) {
                AutoBannedStatusChange::query()->create([
                    'snapshot_id' => $snapshot->id,
                    'sid' => $snapshot->sid,
                    'week' => $snapshot->week,
                    'iso_year' => $snapshot->iso_year,
                    'from_system_status' => $fromStatus,
                    'to_system_status' => $newSystemStatus->value,
                    'change_type' => $changeType,
                    'scrap_status_raw' => $scrapStatusRaw,
                    'scr_row_id' => $scrRow->id,
                    'detected_at' => $now,
                    'scr_scraped_at' => $scrRow->scraped_at,
                ]);
            }

            return [
                'is_new' => $isNew,
                'has_change' => $hasChange,
            ];
        });
    }

    public function shouldPoll(): bool
    {
        if (! Schema::hasTable('auto_banned_poll_logs')) {
            return true;
        }

        $lastPoll = AutoBannedPollLog::query()
            ->where('status', 'completed')
            ->orderByDesc('poll_finished_at')
            ->first();

        if ($lastPoll?->poll_finished_at === null) {
            return true;
        }

        return $lastPoll->poll_finished_at->lte(now()->subMinute());
    }

    public function markHsctSent(AutoBannedStatusSnapshot $snapshot): void
    {
        $snapshot->update([
            'hsct_sync_status' => AutoBannedHsctSyncStatus::Sent,
            'hsct_sent_at' => now(),
        ]);
    }

    public function markHsctConfirmed(AutoBannedStatusSnapshot $snapshot): void
    {
        $snapshot->update([
            'hsct_sync_status' => AutoBannedHsctSyncStatus::Confirmed,
            'hsct_confirmed_at' => now(),
            'ban_status' => AutoBannedBanStatus::ClosedUnbanned,
            'unban_closed_at' => now(),
        ]);
    }
}
