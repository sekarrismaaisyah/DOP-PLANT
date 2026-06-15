<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedSystemStatus;
use App\Enums\AutoBannedUnbanStatus;
use App\Models\AutoBannedPollLog;
use App\Models\AutoBannedStatusChange;
use App\Models\AutoBannedStatusSnapshot;
use App\Models\AutoBannedUnbanRequest;
use App\Models\ScrAutoBannedTbcSap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AutoBannedOverviewService
{
    private const TABLE = 'scr_auto_banned_tbc_sap';

    public function __construct(
        private readonly AutoBannedStatusNormalizer $normalizer,
        private readonly AutoBannedScrapPollService $pollService,
        private readonly AutoBannedStatusResolver $statusResolver,
    ) {}

    /**
     * @return array{site: string, week: string, year: string, perusahaan: string, q: string}
     */
    public function resolveFilters(Request $request): array
    {
        return [
            'site' => trim((string) $request->query('site', '')),
            'week' => $this->normalizer->normalizeWeek((string) $request->query('week', '')),
            'year' => trim((string) $request->query('year', '')),
            'perusahaan' => trim((string) $request->query('perusahaan', '')),
            'q' => trim((string) $request->query('q', '')),
        ];
    }

    public function scrTableAvailable(): bool
    {
        return Schema::hasTable(self::TABLE);
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string, q?: string}  $filters
     * @return array{
     *     filters: array{site: string, week: string, year: string, perusahaan: string, q: string},
     *     period: array{week: string, year: string, scraped_at: ?string},
     *     filterOptions: array{sites: Collection, weeks: Collection, years: Collection, perusahaan: Collection},
     *     stats: array<string, int>,
     *     bannedRows: Collection,
     *     monitoringRows: Collection,
     *     unbanRows: Collection,
     *     syncStats: array<string, int>,
     *     recentChanges: Collection,
     *     pollMeta: array<string, mixed>,
     *     tableAvailable: bool,
     *     trackingAvailable: bool
     * }
     */
    public function buildOverview(array $filters, bool $triggerPoll = true): array
    {
        $tableAvailable = $this->scrTableAvailable();
        $trackingAvailable = $this->pollService->snapshotsTableAvailable();
        $period = $this->resolvePeriod($filters);
        $resolvedFilters = array_merge($filters, [
            'week' => $period['week'],
            'year' => $period['year'],
        ]);

        if ($triggerPoll && $tableAvailable && $trackingAvailable && $this->pollService->shouldPoll()) {
            $this->pollService->poll($resolvedFilters['week'], $resolvedFilters['year']);
        }

        $filterOptions = $this->filterOptions($resolvedFilters);
        $bannedRows = $tableAvailable
            ? $this->bannedRows($resolvedFilters)
            : collect();

        $monitoringRows = $trackingAvailable
            ? $this->monitoringRows($resolvedFilters)
            : $this->monitoringRowsFromScrap($resolvedFilters);

        if ($monitoringRows->isEmpty() && $tableAvailable) {
            $monitoringRows = $this->monitoringRowsFromScrap($resolvedFilters);
        }

        $unbanRows = Schema::hasTable('auto_banned_unban_requests')
            ? $this->unbanRows($resolvedFilters)
            : collect();

        $snapshots = $trackingAvailable
            ? $this->snapshotQuery($resolvedFilters)->get()
            : collect();

        return [
            'filters' => $resolvedFilters,
            'period' => $period,
            'filterOptions' => $filterOptions,
            'stats' => $this->buildStats($bannedRows, $unbanRows, $snapshots),
            'bannedRows' => $bannedRows,
            'monitoringRows' => $monitoringRows,
            'unbanRows' => $unbanRows,
            'syncStats' => $this->statusResolver->buildSyncStats($snapshots),
            'recentChanges' => $this->recentChanges($resolvedFilters),
            'pollMeta' => $this->pollMeta(),
            'tableAvailable' => $tableAvailable,
            'trackingAvailable' => $trackingAvailable,
        ];
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string}  $filters
     * @return array{week: string, year: string, scraped_at: ?string}
     */
    public function resolvePeriod(array $filters): array
    {
        $week = $this->normalizer->normalizeWeek((string) ($filters['week'] ?? ''));
        $year = trim((string) ($filters['year'] ?? ''));

        if ($week !== '' && $year !== '') {
            return [
                'week' => $week,
                'year' => $year,
                'scraped_at' => $this->latestScrapedAt($week, $year),
            ];
        }

        if (! $this->scrTableAvailable()) {
            $now = now()->timezone(config('app.timezone'));

            return [
                'week' => 'W'.$now->isoWeek(),
                'year' => (string) $now->isoWeekYear(),
                'scraped_at' => null,
            ];
        }

        $latest = ScrAutoBannedTbcSap::query()
            ->select(['Week', 'ISO_Year', 'scraped_at'])
            ->whereNotNull('Week')
            ->whereNotNull('ISO_Year')
            ->orderByDesc('scraped_at')
            ->orderByDesc('id')
            ->first();

        if ($latest === null) {
            $now = now()->timezone(config('app.timezone'));

            return [
                'week' => 'W'.$now->isoWeek(),
                'year' => (string) $now->isoWeekYear(),
                'scraped_at' => null,
            ];
        }

        return [
            'week' => $this->normalizer->normalizeWeek((string) $latest->Week),
            'year' => trim((string) $latest->ISO_Year),
            'scraped_at' => $latest->scraped_at?->toDateTimeString(),
        ];
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string}  $filters
     * @return array{sites: Collection<int, string>, weeks: Collection<int, string>, years: Collection<int, string>, perusahaan: Collection<int, string>}
     */
    public function filterOptions(array $filters): array
    {
        if (! $this->scrTableAvailable()) {
            return [
                'sites' => collect(),
                'weeks' => collect(),
                'years' => collect(),
                'perusahaan' => collect(),
            ];
        }

        $year = trim((string) ($filters['year'] ?? ''));

        $sites = ScrAutoBannedTbcSap::query()
            ->select('Site_Dedicated')
            ->whereNotNull('Site_Dedicated')
            ->where('Site_Dedicated', '!=', '')
            ->when($year !== '', fn (Builder $query) => $query->whereRaw('TRIM(CAST(ISO_Year AS CHAR)) = ?', [$year]))
            ->distinct()
            ->orderBy('Site_Dedicated')
            ->pluck('Site_Dedicated')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values();

        $weeks = ScrAutoBannedTbcSap::query()
            ->select('Week')
            ->whereNotNull('Week')
            ->where('Week', '!=', '')
            ->when($year !== '', fn (Builder $query) => $query->whereRaw('TRIM(CAST(ISO_Year AS CHAR)) = ?', [$year]))
            ->distinct()
            ->get()
            ->map(fn ($row) => $this->normalizer->normalizeWeek((string) $row->Week))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $years = ScrAutoBannedTbcSap::query()
            ->select('ISO_Year')
            ->whereNotNull('ISO_Year')
            ->where('ISO_Year', '!=', '')
            ->distinct()
            ->orderByDesc('ISO_Year')
            ->pluck('ISO_Year')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values();

        $perusahaan = ScrAutoBannedTbcSap::query()
            ->select('Perusahaan')
            ->whereNotNull('Perusahaan')
            ->where('Perusahaan', '!=', '')
            ->when($year !== '', fn (Builder $query) => $query->whereRaw('TRIM(CAST(ISO_Year AS CHAR)) = ?', [$year]))
            ->distinct()
            ->orderBy('Perusahaan')
            ->pluck('Perusahaan')
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values();

        return compact('sites', 'weeks', 'years', 'perusahaan');
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string, q?: string}  $filters
     */
    public function bannedQuery(array $filters): Builder
    {
        $query = ScrAutoBannedTbcSap::query()
            ->orderBy('Karyawan')
            ->orderBy('SID');

        $this->applyPeriodFilter($query, $filters);
        $this->applyDimensionFilters($query, $filters);

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function (Builder $builder) use ($like): void {
                $builder->where('Karyawan', 'like', $like)
                    ->orWhere('SID', 'like', $like)
                    ->orWhere('Perusahaan', 'like', $like)
                    ->orWhere('Banned_SID_Reason', 'like', $like);
            });
        }

        return $query;
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string, q?: string}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function bannedRows(array $filters): Collection
    {
        return $this->bannedQuery($filters)
            ->limit(500)
            ->get()
            ->map(fn (ScrAutoBannedTbcSap $row) => $this->mapBannedRow($row))
            ->values();
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string, q?: string}  $filters
     * @return Collection<int, AutoBannedUnbanRequest>
     */
    public function unbanRows(array $filters): Collection
    {
        $query = AutoBannedUnbanRequest::query()
            ->orderByDesc('created_at');

        $site = trim((string) ($filters['site'] ?? ''));
        if ($site !== '') {
            $query->where('site_dedicated', $site);
        }

        $perusahaan = trim((string) ($filters['perusahaan'] ?? ''));
        if ($perusahaan !== '') {
            $query->where('perusahaan', $perusahaan);
        }

        $week = $this->normalizer->normalizeWeek((string) ($filters['week'] ?? ''));
        if ($week !== '') {
            $query->where('week', $week);
        }

        $year = trim((string) ($filters['year'] ?? ''));
        if ($year !== '') {
            $query->where('iso_year', $year);
        }

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function (Builder $builder) use ($like): void {
                $builder->where('sid', 'like', $like)
                    ->orWhere('karyawan', 'like', $like)
                    ->orWhere('perusahaan', 'like', $like)
                    ->orWhere('alasan_pengajuan', 'like', $like);
            });
        }

        return $query->limit(200)->get();
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string, q?: string}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function monitoringRows(array $filters): Collection
    {
        $now = now()->timezone(config('app.timezone'));

        return $this->snapshotQuery($filters)
            ->orderByDesc('banned_detected_at')
            ->orderBy('karyawan')
            ->limit(500)
            ->get()
            ->map(fn (AutoBannedStatusSnapshot $snapshot) => $this->statusResolver->toMonitoringRow($snapshot, $now))
            ->values();
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string, q?: string}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function monitoringRowsFromScrap(array $filters): Collection
    {
        return $this->bannedRows($filters)
            ->map(fn (array $row) => $this->statusResolver->fromScrapRow($row))
            ->values();
    }

    /**
     * @param  array{site?: string, week?: string, year?: string, perusahaan?: string, q?: string}  $filters
     */
    public function snapshotQuery(array $filters): Builder
    {
        $query = AutoBannedStatusSnapshot::query();

        $week = $this->normalizer->normalizeWeek((string) ($filters['week'] ?? ''));
        $year = trim((string) ($filters['year'] ?? ''));

        if ($week !== '') {
            $query->where('week', $week);
        }

        if ($year !== '') {
            $query->where('iso_year', $year);
        }

        $site = trim((string) ($filters['site'] ?? ''));
        if ($site !== '') {
            $query->where('site_dedicated', $site);
        }

        $perusahaan = trim((string) ($filters['perusahaan'] ?? ''));
        if ($perusahaan !== '') {
            $query->where('perusahaan', $perusahaan);
        }

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function (Builder $builder) use ($like): void {
                $builder->where('sid', 'like', $like)
                    ->orWhere('karyawan', 'like', $like)
                    ->orWhere('perusahaan', 'like', $like)
                    ->orWhere('banned_reason', 'like', $like);
            });
        }

        return $query;
    }

    /**
     * @param  array{week?: string, year?: string}  $filters
     * @return Collection<int, AutoBannedStatusChange>
     */
    public function recentChanges(array $filters): Collection
    {
        if (! Schema::hasTable('auto_banned_status_changes')) {
            return collect();
        }

        $query = AutoBannedStatusChange::query()
            ->with('snapshot')
            ->orderByDesc('detected_at')
            ->limit(20);

        $week = $this->normalizer->normalizeWeek((string) ($filters['week'] ?? ''));
        $year = trim((string) ($filters['year'] ?? ''));

        if ($week !== '') {
            $query->where('week', $week);
        }

        if ($year !== '') {
            $query->where('iso_year', $year);
        }

        return $query->get();
    }

    /**
     * @return array{lastPollAt: ?string, lastPollStatus: ?string, lastPollRows: int, lastPollChanges: int}
     */
    public function pollMeta(): array
    {
        if (! Schema::hasTable('auto_banned_poll_logs')) {
            return [
                'lastPollAt' => null,
                'lastPollStatus' => null,
                'lastPollRows' => 0,
                'lastPollChanges' => 0,
            ];
        }

        $lastPoll = AutoBannedPollLog::query()
            ->orderByDesc('poll_started_at')
            ->first();

        return [
            'lastPollAt' => $lastPoll?->poll_finished_at?->format('d M Y H:i') ?? $lastPoll?->poll_started_at?->format('d M Y H:i'),
            'lastPollStatus' => $lastPoll?->status,
            'lastPollRows' => (int) ($lastPoll?->rows_processed ?? 0),
            'lastPollChanges' => (int) ($lastPoll?->status_changes ?? 0),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $bannedRows
     * @param  Collection<int, AutoBannedUnbanRequest>  $unbanRows
     * @param  Collection<int, AutoBannedStatusSnapshot>  $snapshots
     * @return array<string, int>
     */
    private function buildStats(Collection $bannedRows, Collection $unbanRows, Collection $snapshots): array
    {
        $notPassed = $snapshots->where('system_status', AutoBannedSystemStatus::NotPassed);

        return [
            'totalBanned' => $notPassed->isNotEmpty() ? $notPassed->count() : $bannedRows->count(),
            'totalSid' => $notPassed->isNotEmpty()
                ? $notPassed->pluck('sid')->filter()->unique()->count()
                : $bannedRows->pluck('sid')->filter()->unique()->count(),
            'pendingUnban' => $unbanRows->where('status', AutoBannedUnbanStatus::Pending)->count(),
            'approvedUnban' => $unbanRows->where('status', AutoBannedUnbanStatus::Approved)->count(),
            'rejectedUnban' => $unbanRows->where('status', AutoBannedUnbanStatus::Rejected)->count(),
            'statusChanges' => Schema::hasTable('auto_banned_status_changes')
                ? AutoBannedStatusChange::query()->count()
                : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapBannedRow(ScrAutoBannedTbcSap $row): array
    {
        $status = trim((string) ($row->Status_Banned_SID_SAP ?? ''));

        return [
            'id' => $row->id,
            'karyawan' => trim((string) ($row->Karyawan ?? '')),
            'perusahaan' => trim((string) ($row->Perusahaan ?? '')),
            'sid' => trim((string) ($row->SID ?? '')),
            'site' => trim((string) ($row->Site_Dedicated ?? '')),
            'week' => $this->normalizer->normalizeWeek((string) ($row->Week ?? '')),
            'year' => trim((string) ($row->ISO_Year ?? '')),
            'sap' => trim((string) ($row->SAP ?? '')),
            'sap2' => trim((string) ($row->SAP2 ?? '')),
            'sapPercentile' => trim((string) ($row->Percentile_of_SAP ?? '')),
            'tbc' => trim((string) ($row->Distinct_count_of_tbc_ver2 ?? '')),
            'reason' => trim((string) ($row->Banned_SID_Reason ?? '')),
            'status' => $status,
            'statusTone' => $this->resolveStatusTone($status),
            'scrapedAt' => $row->scraped_at?->format('d M Y H:i'),
        ];
    }

    private function resolveStatusTone(string $status): string
    {
        $normalized = strtoupper($status);

        if ($normalized === '') {
            return 'slate';
        }

        if (str_contains($normalized, 'BANNED')) {
            return 'danger';
        }

        if (str_contains($normalized, 'UNBAN') || str_contains($normalized, 'CLEAR')) {
            return 'success';
        }

        return 'warning';
    }

    /**
     * @param  array{week?: string, year?: string}  $filters
     */
    private function applyPeriodFilter(Builder $query, array $filters): void
    {
        $week = $this->normalizer->normalizeWeek((string) ($filters['week'] ?? ''));
        $year = trim((string) ($filters['year'] ?? ''));

        if ($week !== '') {
            $query->whereRaw('UPPER(TRIM(CAST(Week AS CHAR))) = ?', [$week]);
        }

        if ($year !== '') {
            $query->whereRaw('TRIM(CAST(ISO_Year AS CHAR)) = ?', [$year]);
        }
    }

    /**
     * @param  array{site?: string, perusahaan?: string}  $filters
     */
    private function applyDimensionFilters(Builder $query, array $filters): void
    {
        $site = trim((string) ($filters['site'] ?? ''));
        if ($site !== '') {
            $query->where('Site_Dedicated', $site);
        }

        $perusahaan = trim((string) ($filters['perusahaan'] ?? ''));
        if ($perusahaan !== '') {
            $query->where('Perusahaan', $perusahaan);
        }
    }

    private function latestScrapedAt(string $week, string $year): ?string
    {
        if (! $this->scrTableAvailable()) {
            return null;
        }

        $row = ScrAutoBannedTbcSap::query()
            ->select('scraped_at')
            ->whereRaw('UPPER(TRIM(CAST(Week AS CHAR))) = ?', [$week])
            ->whereRaw('TRIM(CAST(ISO_Year AS CHAR)) = ?', [$year])
            ->orderByDesc('scraped_at')
            ->first();

        return $row?->scraped_at?->toDateTimeString();
    }
}
