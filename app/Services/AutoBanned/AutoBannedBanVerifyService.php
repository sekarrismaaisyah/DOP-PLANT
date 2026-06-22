<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedBanStatus;
use App\Enums\AutoBannedHsctSyncStatus;
use App\Enums\AutoBannedSystemStatus;
use App\Models\AutoBannedHsctCampaign;
use App\Models\AutoBannedStatusSnapshot;
use App\Services\DatabaseConnectionService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AutoBannedBanVerifyService
{
    public function __construct(
        private readonly AutoBannedStatusNormalizer $normalizer,
        private readonly AutoBannedHsctEmailService $hsctEmailService,
        private readonly DatabaseConnectionService $databaseConnectionService,
    ) {}

    public function verifyTableAvailable(): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        if (! $this->isConnectionReady()) {
            return false;
        }

        try {
            return $this->verifySourceExists();
        } catch (Throwable $exception) {
            Log::warning('AutoBanned ban verify source check failed', [
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function snapshotsTableAvailable(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasTable('auto_banned_status_snapshots');
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

        if (! $this->isConfigured()) {
            return [
                'checked' => 0,
                'confirmed' => 0,
                'skipped' => 0,
                'message' => 'Sumber verifikasi banned belum dikonfigurasi (AUTO_BANNED_VERIFY_TABLE).',
            ];
        }

        if (! $this->isConnectionReady()) {
            return [
                'checked' => 0,
                'confirmed' => 0,
                'skipped' => 0,
                'message' => $this->connectionUnavailableMessage(),
            ];
        }

        try {
            if (! $this->verifySourceExists()) {
                return [
                    'checked' => 0,
                    'confirmed' => 0,
                    'skipped' => 0,
                    'message' => 'View/tabel verifikasi tidak ditemukan: '.$this->qualifiedSourceName(),
                ];
            }
        } catch (Throwable $exception) {
            Log::error('AutoBanned ban verify source lookup failed', [
                'error' => $exception->getMessage(),
            ]);

            return [
                'checked' => 0,
                'confirmed' => 0,
                'skipped' => 0,
                'message' => 'Gagal mengakses sumber verifikasi: '.$exception->getMessage(),
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

        try {
            $permitStatusMap = $this->fetchPermitStatusMap($snapshots->pluck('sid'));
        } catch (Throwable $exception) {
            Log::error('AutoBanned ban verify query failed', [
                'error' => $exception->getMessage(),
            ]);

            return [
                'checked' => $snapshots->count(),
                'confirmed' => 0,
                'skipped' => $snapshots->count(),
                'message' => 'Gagal query status permit BCSID: '.$exception->getMessage(),
            ];
        }

        $confirmed = 0;
        $skipped = 0;

        foreach ($snapshots as $snapshot) {
            $sidKey = strtoupper(trim($snapshot->sid));
            $verifyRow = $permitStatusMap[$sidKey] ?? null;

            if ($verifyRow === null) {
                $skipped++;

                continue;
            }

            $statusRaw = (string) ($verifyRow->status_permit ?? '');

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
            'message' => "Verifikasi selesai: {$confirmed} SID dikonfirmasi banned (status_permit NOT PASSED), {$skipped} belum terbanned.",
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

    /**
     * @param  Collection<int, string>  $sids
     * @return array<string, object{status_permit: mixed, update_date_permit: mixed}>
     */
    private function fetchPermitStatusMap(Collection $sids): array
    {
        $normalizedSids = $sids
            ->map(fn (string $sid): string => strtoupper(trim($sid)))
            ->filter(fn (string $sid): bool => $sid !== '')
            ->unique()
            ->values()
            ->all();

        if ($normalizedSids === []) {
            return [];
        }

        $connection = $this->connectionName();
        $qualifiedSource = $this->qualifiedSourceName();
        $sidColumn = $this->sidColumn();
        $statusColumn = $this->statusColumn();
        $placeholders = implode(', ', array_fill(0, count($normalizedSids), '?'));

        $sql = <<<SQL
            SELECT DISTINCT ON (UPPER(TRIM({$sidColumn}::text)))
                UPPER(TRIM({$sidColumn}::text)) AS sid_key,
                {$statusColumn} AS status_permit,
                update_date_permit
            FROM {$qualifiedSource}
            WHERE UPPER(TRIM({$sidColumn}::text)) IN ({$placeholders})
            ORDER BY UPPER(TRIM({$sidColumn}::text)), id DESC
        SQL;

        $rows = DB::connection($connection)->select($sql, $normalizedSids);

        $map = [];
        foreach ($rows as $row) {
            $key = strtoupper(trim((string) ($row->sid_key ?? '')));
            if ($key !== '') {
                $map[$key] = $row;
            }
        }

        return $map;
    }

    private function confirmBanned(AutoBannedStatusSnapshot $snapshot, Carbon $now): void
    {
        $snapshot->update([
            'hsct_sync_status' => AutoBannedHsctSyncStatus::Confirmed,
            'hsct_confirmed_at' => $now,
            'ban_status' => AutoBannedBanStatus::CloseBanned,
        ]);
    }

    private function isConfigured(): bool
    {
        return $this->verifyTableName() !== '';
    }

    private function isConnectionReady(): bool
    {
        if ($this->connectionName() !== 'pgsql_ssh') {
            return true;
        }

        if (! config('auto_banned.ban_verify.require_ssh_tunnel', true)) {
            return true;
        }

        return $this->databaseConnectionService->isTunnelActive();
    }

    private function connectionUnavailableMessage(): string
    {
        $localPort = config('database.connections.pgsql_ssh.local_port', 5433);

        return "SSH tunnel PostgreSQL belum aktif (port {$localPort}). "
            .'Jalankan setup-ssh-tunnel.bat/ps1 terlebih dahulu, lalu ulangi auto-banned:verify-banned.';
    }

    private function verifySourceExists(): bool
    {
        $connection = $this->connectionName();
        $schema = $this->verifySchema();
        $table = $this->verifyTableName();

        $result = DB::connection($connection)->select(
            'SELECT EXISTS (
                SELECT 1
                FROM information_schema.tables
                WHERE table_schema = ?
                  AND table_name = ?
            ) AS exists',
            [$schema, $table]
        );

        if ((bool) ($result[0]->exists ?? false)) {
            return true;
        }

        $viewResult = DB::connection($connection)->select(
            'SELECT EXISTS (
                SELECT 1
                FROM information_schema.views
                WHERE table_schema = ?
                  AND table_name = ?
            ) AS exists',
            [$schema, $table]
        );

        return (bool) ($viewResult[0]->exists ?? false);
    }

    private function connectionName(): string
    {
        return trim((string) config('auto_banned.ban_verify.connection', 'pgsql_ssh'));
    }

    private function verifySchema(): string
    {
        return trim((string) config('auto_banned.ban_verify.schema', 'bcsid'));
    }

    private function verifyTableName(): string
    {
        return trim((string) config('auto_banned.ban_verify.table', ''));
    }

    private function qualifiedSourceName(): string
    {
        $schema = $this->verifySchema();
        $table = $this->verifyTableName();

        if ($schema === '') {
            return $table;
        }

        return $schema.'.'.$table;
    }

    private function sidColumn(): string
    {
        return (string) config('auto_banned.ban_verify.sid_column', 'kode_sid');
    }

    private function statusColumn(): string
    {
        return (string) config('auto_banned.ban_verify.status_column', 'status_permit');
    }
}
