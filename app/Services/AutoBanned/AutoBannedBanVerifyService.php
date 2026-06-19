<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedBanStatus;
use App\Enums\AutoBannedHsctSyncStatus;
use App\Enums\AutoBannedSystemStatus;
use App\Models\AutoBannedHsctCampaign;
use App\Models\AutoBannedStatusSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AutoBannedBanVerifyService
{
    public function __construct(
        private readonly AutoBannedStatusNormalizer $normalizer,
        private readonly AutoBannedHsctEmailService $hsctEmailService,
    ) {}

    public function verifyTableAvailable(): bool
    {
        $table = $this->verifyTableName();

        return $table !== '' && Schema::hasTable($table);
    }

    public function snapshotsTableAvailable(): bool
    {
        return Schema::hasTable('auto_banned_status_snapshots');
    }

    /**
     * @return array{checked: int, confirmed: int, skipped: int, message: string}
     */
    public function verify(?string $week = null, ?string $year = null): array
    {
        if (! $this->snapshotsTableAvailable()) {
            return [
                'checked' => 0,
                'confirmed' => 0,
                'skipped' => 0,
                'message' => 'Tabel auto_banned_status_snapshots belum tersedia.',
            ];
        }

        if (! $this->verifyTableAvailable()) {
            return [
                'checked' => 0,
                'confirmed' => 0,
                'skipped' => 0,
                'message' => 'Tabel verifikasi banned belum dikonfigurasi (AUTO_BANNED_VERIFY_TABLE).',
            ];
        }

        $period = $this->hsctEmailService->resolvePeriod($week, $year);
        $now = now()->timezone(config('auto_banned.hsct.timezone', 'Asia/Makassar'));

        $snapshots = AutoBannedStatusSnapshot::query()
            ->where('week', $period['week'])
            ->where('iso_year', $period['year'])
            ->where('system_status', AutoBannedSystemStatus::NotPassed)
            ->where('hsct_sync_status', AutoBannedHsctSyncStatus::Sent)
            ->whereNull('hsct_confirmed_at')
            ->orderBy('sid')
            ->get();

        if ($snapshots->isEmpty()) {
            return [
                'checked' => 0,
                'confirmed' => 0,
                'skipped' => 0,
                'message' => 'Tidak ada SID terkirim HSECT yang menunggu verifikasi banned.',
            ];
        }

        $confirmed = 0;
        $skipped = 0;

        foreach ($snapshots as $snapshot) {
            $verifyRow = $this->findVerifyRow($snapshot, $period['week'], $period['year']);

            if ($verifyRow === null) {
                $skipped++;

                continue;
            }

            $statusRaw = (string) ($verifyRow->{$this->statusColumn()} ?? '');

            if (! $this->isExecutedBan($statusRaw)) {
                $skipped++;

                continue;
            }

            $this->confirmBanned($snapshot, $now);
            $confirmed++;
        }

        $campaign = $this->hsctEmailService->findCampaign($period['week'], $period['year']);
        if ($campaign instanceof AutoBannedHsctCampaign) {
            $this->hsctEmailService->syncCampaignConfirmation($campaign);
        }

        return [
            'checked' => $snapshots->count(),
            'confirmed' => $confirmed,
            'skipped' => $skipped,
            'message' => "Verifikasi selesai: {$confirmed} SID dikonfirmasi banned, {$skipped} belum terbanned.",
        ];
    }

    public function isExecutedBan(?string $rawStatus): bool
    {
        $executedValues = config('auto_banned.ban_verify.executed_values', []);

        if ($executedValues !== []) {
            $normalized = strtoupper(trim((string) $rawStatus));

            foreach ($executedValues as $value) {
                if ($normalized === strtoupper(trim((string) $value))) {
                    return true;
                }
            }
        }

        $normalized = strtoupper(trim((string) $rawStatus));

        if ($normalized === '') {
            return false;
        }

        if (str_contains($normalized, 'UNBAN') || str_contains($normalized, 'CLEAR')) {
            return false;
        }

        if (! str_contains($normalized, 'BANNED')) {
            return false;
        }

        if (str_contains($normalized, 'NOT PASS') || str_contains($normalized, 'NOT_PASS')) {
            return false;
        }

        return true;
    }

    private function confirmBanned(AutoBannedStatusSnapshot $snapshot, Carbon $now): void
    {
        $snapshot->update([
            'hsct_sync_status' => AutoBannedHsctSyncStatus::Confirmed,
            'hsct_confirmed_at' => $now,
            'ban_status' => AutoBannedBanStatus::CloseBanned,
        ]);
    }

    /**
     * @return object|null
     */
    private function findVerifyRow(AutoBannedStatusSnapshot $snapshot, string $week, string $year): ?object
    {
        $query = DB::table($this->verifyTableName())
            ->where($this->sidColumn(), $snapshot->sid);

        $weekColumn = (string) config('auto_banned.ban_verify.week_column', '');
        if ($weekColumn !== '' && Schema::hasColumn($this->verifyTableName(), $weekColumn)) {
            $query->whereRaw(
                'UPPER(TRIM(CAST(`'.$weekColumn.'` AS CHAR))) = ?',
                [$this->normalizer->normalizeWeek($week)]
            );
        }

        $yearColumn = (string) config('auto_banned.ban_verify.year_column', '');
        if ($yearColumn !== '' && Schema::hasColumn($this->verifyTableName(), $yearColumn)) {
            $query->whereRaw(
                'TRIM(CAST(`'.$yearColumn.'` AS CHAR)) = ?',
                [trim($year)]
            );
        }

        if (Schema::hasColumn($this->verifyTableName(), 'id')) {
            $query->orderByDesc('id');
        }

        return $query->first();
    }

    private function verifyTableName(): string
    {
        return trim((string) config('auto_banned.ban_verify.table', ''));
    }

    private function sidColumn(): string
    {
        return (string) config('auto_banned.ban_verify.sid_column', 'SID');
    }

    private function statusColumn(): string
    {
        return (string) config('auto_banned.ban_verify.status_column', 'Status_Banned_SID_SAP');
    }
}
