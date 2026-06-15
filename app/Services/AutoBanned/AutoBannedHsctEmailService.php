<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedHsctCampaignStatus;
use App\Enums\AutoBannedHsctEmailType;
use App\Enums\AutoBannedHsctSyncStatus;
use App\Enums\AutoBannedSystemStatus;
use App\Mail\AutoBannedHsctEmailMail;
use App\Models\AutoBannedHsctCampaign;
use App\Models\AutoBannedHsctCampaignItem;
use App\Models\AutoBannedHsctEmailLog;
use App\Models\AutoBannedStatusSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class AutoBannedHsctEmailService
{
    public function __construct(
        private readonly AutoBannedMonitoringDummyService $dummyService,
        private readonly AutoBannedStatusNormalizer $normalizer,
    ) {}

    public function tablesAvailable(): bool
    {
        return Schema::hasTable('auto_banned_hsct_campaigns')
            && Schema::hasTable('auto_banned_hsct_campaign_items')
            && Schema::hasTable('auto_banned_hsct_email_logs');
    }

    /**
     * @return array{action: string, message: string, sent: bool}
     */
    public function runScheduled(?string $week = null, ?string $year = null, bool $force = false): array
    {
        if (! $this->tablesAvailable()) {
            return ['action' => 'skip', 'message' => 'Tabel campaign email belum tersedia.', 'sent' => false];
        }

        $period = $this->resolvePeriod($week, $year);
        $now = Carbon::now(config('auto_banned.hsct.timezone', 'Asia/Makassar'));
        $campaign = $this->findCampaign($period['week'], $period['year']);

        if ($campaign === null && ($force || $this->isInitialSendDay($now))) {
            return $this->sendInitialEmail($period['week'], $period['year'], $force);
        }

        if ($campaign !== null && $campaign->status === AutoBannedHsctCampaignStatus::Active) {
            if ($force || ! $this->isInitialSendDay($now)) {
                return $this->sendReminderEmail($campaign, $force);
            }

            return ['action' => 'skip', 'message' => 'Hari Selasa — email awal sudah ada untuk periode ini.', 'sent' => false];
        }

        if ($campaign?->status === AutoBannedHsctCampaignStatus::Completed) {
            return ['action' => 'skip', 'message' => 'Semua SID sudah dikonfirmasi banned.', 'sent' => false];
        }

        return ['action' => 'skip', 'message' => 'Belum waktunya email awal (Selasa).', 'sent' => false];
    }

    /**
     * @return array{action: string, message: string, sent: bool}
     */
    public function sendInitialEmail(string $week, string $year, bool $force = false): array
    {
        if ($this->findCampaign($week, $year) !== null && ! $force) {
            return ['action' => 'skip', 'message' => 'Campaign periode ini sudah ada.', 'sent' => false];
        }

        $recipients = $this->recipients();
        if ($recipients === []) {
            return ['action' => 'error', 'message' => 'AUTO_BANNED_HSCT_EMAILS belum dikonfigurasi.', 'sent' => false];
        }

        $sourceItems = $this->resolveBannedSourceItems($week, $year);
        if ($sourceItems->isEmpty()) {
            return ['action' => 'skip', 'message' => 'Tidak ada data banned untuk dikirim.', 'sent' => false];
        }

        return DB::transaction(function () use ($week, $year, $recipients, $sourceItems, $force): array {
            if ($force) {
                $existing = $this->findCampaign($week, $year);
                if ($existing !== null) {
                    $existing->items()->delete();
                    $existing->emailLogs()->delete();
                    $existing->delete();
                }
            }

            $campaign = AutoBannedHsctCampaign::query()->create([
                'week' => $week,
                'iso_year' => $year,
                'status' => AutoBannedHsctCampaignStatus::Active,
                'total_items' => $sourceItems->count(),
                'confirmed_items' => 0,
                'reminder_count' => 0,
                'initial_sent_at' => now(),
            ]);

            foreach ($sourceItems as $item) {
                AutoBannedHsctCampaignItem::query()->create([
                    'campaign_id' => $campaign->id,
                    'snapshot_id' => $item['snapshot_id'],
                    'sid' => $item['sid'],
                    'karyawan' => $item['karyawan'],
                    'perusahaan' => $item['perusahaan'],
                    'site_dedicated' => $item['site'],
                    'banned_reason' => $item['reason'],
                    'is_confirmed' => false,
                ]);

                if ($item['snapshot_id'] !== null) {
                    AutoBannedStatusSnapshot::query()
                        ->where('id', $item['snapshot_id'])
                        ->update([
                            'hsct_sync_status' => AutoBannedHsctSyncStatus::Sent->value,
                            'hsct_sent_at' => now(),
                        ]);
                }
            }

            $payload = $this->formatPayload($sourceItems);
            $sent = $this->dispatchMail(
                recipients: $recipients,
                emailType: AutoBannedHsctEmailType::Initial,
                reminderNumber: 1,
                week: $week,
                year: $year,
                employees: $payload,
                totalInitial: $sourceItems->count(),
                confirmedCount: 0,
                pendingCount: $sourceItems->count(),
            );

            AutoBannedHsctEmailLog::query()->create([
                'campaign_id' => $campaign->id,
                'email_type' => AutoBannedHsctEmailType::Initial,
                'reminder_number' => 1,
                'week' => $week,
                'iso_year' => $year,
                'recipients' => implode(', ', $recipients),
                'total_in_list' => $sourceItems->count(),
                'pending_count' => $sourceItems->count(),
                'confirmed_count' => 0,
                'payload' => $payload,
                'status' => $sent ? 'sent' : 'failed',
                'error_message' => $sent ? null : 'Gagal kirim email',
                'sent_at' => now(),
            ]);

            return [
                'action' => 'initial',
                'message' => $sent
                    ? "Email awal terkirim ke HSECT ({$sourceItems->count()} SID)."
                    : 'Campaign dibuat tetapi email gagal dikirim.',
                'sent' => $sent,
            ];
        });
    }

    /**
     * @return array{action: string, message: string, sent: bool}
     */
    public function sendReminderEmail(AutoBannedHsctCampaign $campaign, bool $force = false): array
    {
        $recipients = $this->recipients();
        if ($recipients === []) {
            return ['action' => 'error', 'message' => 'AUTO_BANNED_HSCT_EMAILS belum dikonfigurasi.', 'sent' => false];
        }

        $this->syncCampaignConfirmation($campaign);
        $campaign->refresh();

        $pendingItems = $campaign->items()->where('is_confirmed', false)->get();
        if ($pendingItems->isEmpty()) {
            $campaign->update([
                'status' => AutoBannedHsctCampaignStatus::Completed,
                'completed_at' => now(),
            ]);

            return ['action' => 'completed', 'message' => 'Semua SID sudah banned — campaign ditutup.', 'sent' => false];
        }

        if (! $force && $campaign->last_reminder_at !== null && $campaign->last_reminder_at->isToday()) {
            return ['action' => 'skip', 'message' => 'Reminder hari ini sudah dikirim.', 'sent' => false];
        }

        $reminderNumber = $campaign->reminder_count + 2;
        $payload = $pendingItems->map(fn (AutoBannedHsctCampaignItem $item) => [
            'sid' => $item->sid,
            'karyawan' => $item->karyawan,
            'site' => $item->site_dedicated,
            'perusahaan' => $item->perusahaan,
            'reason' => $item->banned_reason,
        ])->values()->all();

        $sent = $this->dispatchMail(
            recipients: $recipients,
            emailType: AutoBannedHsctEmailType::Reminder,
            reminderNumber: $reminderNumber,
            week: $campaign->week,
            year: $campaign->iso_year,
            employees: $payload,
            totalInitial: $campaign->total_items,
            confirmedCount: $campaign->confirmed_items,
            pendingCount: $pendingItems->count(),
        );

        AutoBannedHsctEmailLog::query()->create([
            'campaign_id' => $campaign->id,
            'email_type' => AutoBannedHsctEmailType::Reminder,
            'reminder_number' => $reminderNumber,
            'week' => $campaign->week,
            'iso_year' => $campaign->iso_year,
            'recipients' => implode(', ', $recipients),
            'total_in_list' => $pendingItems->count(),
            'pending_count' => $pendingItems->count(),
            'confirmed_count' => $campaign->confirmed_items,
            'payload' => $payload,
            'status' => $sent ? 'sent' : 'failed',
            'error_message' => $sent ? null : 'Gagal kirim email',
            'sent_at' => now(),
        ]);

        $campaign->update([
            'reminder_count' => $campaign->reminder_count + 1,
            'last_reminder_at' => now(),
        ]);

        return [
            'action' => 'reminder',
            'message' => $sent
                ? "Reminder #{$reminderNumber} terkirim ({$pendingItems->count()} SID belum banned)."
                : "Reminder #{$reminderNumber} gagal dikirim.",
            'sent' => $sent,
        ];
    }

    public function syncCampaignConfirmation(AutoBannedHsctCampaign $campaign): void
    {
        $items = $campaign->items()->get();

        foreach ($items as $item) {
            if ($item->is_confirmed) {
                continue;
            }

            $snapshot = null;
            if ($item->snapshot_id !== null) {
                $snapshot = AutoBannedStatusSnapshot::query()->find($item->snapshot_id);
            } else {
                $snapshot = AutoBannedStatusSnapshot::query()
                    ->where('sid', $item->sid)
                    ->where('week', $campaign->week)
                    ->where('iso_year', $campaign->iso_year)
                    ->first();
            }

            if ($snapshot !== null && $snapshot->hsct_sync_status === AutoBannedHsctSyncStatus::Confirmed) {
                $item->update([
                    'is_confirmed' => true,
                    'confirmed_at' => $snapshot->hsct_confirmed_at ?? now(),
                ]);
            }
        }

        $confirmed = $campaign->items()->where('is_confirmed', true)->count();
        $campaign->update(['confirmed_items' => $confirmed]);

        if ($confirmed >= $campaign->total_items && $campaign->total_items > 0) {
            $campaign->update([
                'status' => AutoBannedHsctCampaignStatus::Completed,
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * @return Collection<int, array{snapshot_id: ?int, sid: string, karyawan: string, site: string, perusahaan: string, reason: string}>
     */
    public function resolveBannedSourceItems(string $week, string $year): Collection
    {
        if (Schema::hasTable('auto_banned_status_snapshots')) {
            $snapshots = AutoBannedStatusSnapshot::query()
                ->where('week', $week)
                ->where('iso_year', $year)
                ->where('system_status', AutoBannedSystemStatus::NotPassed)
                ->orderBy('karyawan')
                ->get();

            if ($snapshots->isNotEmpty()) {
                return $snapshots->map(fn (AutoBannedStatusSnapshot $s) => [
                    'snapshot_id' => $s->id,
                    'sid' => $s->sid,
                    'karyawan' => $s->karyawan ?? '',
                    'site' => $s->site_dedicated ?? '',
                    'perusahaan' => $s->perusahaan ?? '',
                    'reason' => $s->banned_reason ?? 'Tidak ada SAP',
                ]);
            }
        }

        if (config('auto_banned.hsct.use_dummy_when_empty', true)) {
            return $this->dummyService->lifecycleRows()
                ->filter(fn (array $row) => $row['systemStatus'] === AutoBannedSystemStatus::NotPassed)
                ->map(fn (array $row) => [
                    'snapshot_id' => null,
                    'sid' => $row['sid'],
                    'karyawan' => $row['karyawan'],
                    'site' => '',
                    'perusahaan' => '',
                    'reason' => 'Tidak ada SAP',
                ])
                ->values();
        }

        return collect();
    }

    public function findCampaign(string $week, string $year): ?AutoBannedHsctCampaign
    {
        return AutoBannedHsctCampaign::query()
            ->where('week', $week)
            ->where('iso_year', $year)
            ->first();
    }

    /**
     * @return array{week: string, year: string}
     */
    public function resolvePeriod(?string $week, ?string $year): array
    {
        $normalizedWeek = $this->normalizer->normalizeWeek((string) ($week ?? ''));
        $normalizedYear = trim((string) ($year ?? ''));

        if ($normalizedWeek !== '' && $normalizedYear !== '') {
            return ['week' => $normalizedWeek, 'year' => $normalizedYear];
        }

        $now = Carbon::now(config('auto_banned.hsct.timezone', 'Asia/Makassar'));

        return [
            'week' => 'W'.$now->isoWeek(),
            'year' => (string) $now->isoWeekYear(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function recipients(): array
    {
        return config('auto_banned.hsct.recipients', []);
    }

    public function isInitialSendDay(Carbon $now): bool
    {
        return $now->dayOfWeek === (int) config('auto_banned.hsct.initial_day', 2);
    }

    /**
     * @return Collection<int, AutoBannedHsctEmailLog>
     */
    public function emailHistory(?string $week = null, ?string $year = null, int $limit = 20): Collection
    {
        if (! Schema::hasTable('auto_banned_hsct_email_logs')) {
            return collect();
        }

        $query = AutoBannedHsctEmailLog::query()
            ->with('campaign')
            ->orderByDesc('sent_at')
            ->limit($limit);

        if ($week !== null && $week !== '') {
            $query->where('week', $this->normalizer->normalizeWeek($week));
        }

        if ($year !== null && $year !== '') {
            $query->where('iso_year', trim($year));
        }

        return $query->get();
    }

    /**
     * @return Collection<int, AutoBannedHsctCampaignItem>
     */
    public function pendingCampaignItems(?string $week = null, ?string $year = null): Collection
    {
        if (! Schema::hasTable('auto_banned_hsct_campaign_items')) {
            return collect();
        }

        $period = $this->resolvePeriod($week, $year);
        $campaign = $this->findCampaign($period['week'], $period['year']);

        if ($campaign === null) {
            return collect();
        }

        $this->syncCampaignConfirmation($campaign);

        return $campaign->items()->where('is_confirmed', false)->orderBy('karyawan')->get();
    }

    public function confirmCampaignItem(AutoBannedHsctCampaignItem $item): void
    {
        $item->update([
            'is_confirmed' => true,
            'confirmed_at' => now(),
        ]);

        $campaign = $item->campaign;
        if ($campaign !== null) {
            $this->syncCampaignConfirmation($campaign);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function activeCampaignSummary(?string $week = null, ?string $year = null): ?array
    {
        $period = $this->resolvePeriod($week, $year);
        $campaign = $this->findCampaign($period['week'], $period['year']);

        if ($campaign === null) {
            return null;
        }

        $this->syncCampaignConfirmation($campaign);
        $campaign->refresh();

        return [
            'week' => $campaign->week,
            'year' => $campaign->iso_year,
            'status' => $campaign->status,
            'totalItems' => $campaign->total_items,
            'confirmedItems' => $campaign->confirmed_items,
            'pendingItems' => max(0, $campaign->total_items - $campaign->confirmed_items),
            'reminderCount' => $campaign->reminder_count,
            'initialSentAt' => $campaign->initial_sent_at?->format('d M Y H:i'),
            'lastReminderAt' => $campaign->last_reminder_at?->format('d M Y H:i'),
            'completedAt' => $campaign->completed_at?->format('d M Y H:i'),
            'allBanned' => $campaign->status === AutoBannedHsctCampaignStatus::Completed,
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $items
     * @return array<int, array{sid: string, karyawan: string, site: string, perusahaan: string, reason: string}>
     */
    private function formatPayload(Collection|array $items): array
    {
        $collection = $items instanceof Collection ? $items : collect($items);

        return $collection->map(fn (array $item) => [
            'sid' => $item['sid'],
            'karyawan' => $item['karyawan'] ?? '',
            'site' => $item['site'] ?? '',
            'perusahaan' => $item['perusahaan'] ?? '',
            'reason' => $item['reason'] ?? '',
        ])->values()->all();
    }

    /**
     * @param  array<int, string>  $recipients
     * @param  array<int, array{sid: string, karyawan: string, site: string, perusahaan: string, reason: string}>  $employees
     */
    private function dispatchMail(
        array $recipients,
        AutoBannedHsctEmailType $emailType,
        int $reminderNumber,
        string $week,
        string $year,
        array $employees,
        int $totalInitial,
        int $confirmedCount,
        int $pendingCount,
    ): bool {
        try {
            $mailable = new AutoBannedHsctEmailMail(
                emailType: $emailType,
                reminderNumber: $reminderNumber,
                week: $week,
                isoYear: $year,
                employees: $employees,
                totalInitial: $totalInitial,
                confirmedCount: $confirmedCount,
                pendingCount: $pendingCount,
            );

            foreach ($recipients as $email) {
                Mail::to($email)->send($mailable);
            }

            return true;
        } catch (\Throwable $exception) {
            Log::error('AutoBanned HSECT email failed', [
                'type' => $emailType->value,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
