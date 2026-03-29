<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Support\PeerPressure\KategoriDeviasiBucket;
use Carbon\Carbon;

/**
 * Chart trend: per minggu ISO dalam satu bulan, atau agregasi per bulan untuk seluruh rentang 2025–2026.
 * Tiap batang dipecah per kategori deviasi (peer pressure) untuk 5 kelompok utama + Lainnya.
 */
final class GetPeerPressureDashboardWeeklyTrendAction
{
    public const MIN_YEAR = 2025;

    public const MAX_YEAR = 2026;

    /** Urutan tumpukan dari bawah ke atas */
    private const DEVIATION_KEYS = [
        'tidak_speak_up_fatigue',
        'blindspot_to_be_concerned',
        'pelanggaran_pspp',
        'pelanggaran_golden_rules',
        'insiden',
        'lainnya',
    ];

    /** @var array<int, string> */
    private const MONTHS_ID = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /** @var array<int, string> */
    private const MONTHS_SHORT = [
        1 => 'Jan', 2 => 'Peb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    /**
     * @return array{
     *   weeks: list<array<string, mixed>>,
     *   max_count: int,
     *   avg_count: float,
     *   target_line_bottom_pct: float,
     *   period_caption: string,
     *   chart_year: int|null,
     *   chart_month: int|null,
     *   month_label: string,
     *   period_scope: 'all'|'month',
     *   chart_granularity: 'week'|'month',
     *   avg_legend_label: string,
     *   deviation_categories: list<array{key: string, label: string, color: string}>
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null): array
    {
        if ($year === null || $month === null) {
            return $this->buildAllCalendarMonths();
        }

        return $this->buildSingleMonthWeeks(
            max(self::MIN_YEAR, min(self::MAX_YEAR, $year)),
            max(1, min(12, $month))
        );
    }

    private function buildAllCalendarMonths(): array
    {
        $rangeStart = Carbon::create(self::MIN_YEAR, 1, 1)->startOfMonth();

        $maxRaw = PeerPressureKejadianEdukasi::query()->max('tanggal_temuan');
        if ($maxRaw === null) {
            return $this->finalizeRows(
                [],
                'Semua data (per bulan)',
                null,
                null,
                '',
                'all',
                'month',
                'Rata-rata bulanan'
            );
        }

        $maxMonth = Carbon::parse((string) $maxRaw)->startOfMonth();
        $capEnd = Carbon::create(self::MAX_YEAR, 12, 1)->startOfMonth();
        $rangeEnd = $maxMonth->copy()->min($capEnd);

        if ($rangeEnd->lt($rangeStart)) {
            return $this->finalizeRows(
                [],
                'Semua data (per bulan)',
                null,
                null,
                '',
                'all',
                'month',
                'Rata-rata bulanan'
            );
        }

        $rows = [];
        $cursor = $rangeStart->copy();
        while ($cursor->lte($rangeEnd)) {
            $y = (int) $cursor->year;
            $m = (int) $cursor->month;

            $monthStart = $cursor->copy()->startOfMonth()->startOfDay();
            $monthEnd = $cursor->copy()->endOfMonth();

            $byCategory = $this->aggregateCategoriesBetween(
                $monthStart->toDateString(),
                $monthEnd->toDateString()
            );
            $count = array_sum($byCategory);

            $shortY = substr((string) $y, -2);
            $label = (self::MONTHS_SHORT[$m] ?? (string) $m).' '.$shortY;

            $rows[] = [
                'label' => $label,
                'count' => $count,
                'by_category' => $byCategory,
                'range_short' => $monthStart->format('d M').' – '.$monthEnd->format('d M Y'),
                'iso_week_year' => $y,
            ];

            $cursor->addMonth();
        }

        $shortStart = (self::MONTHS_SHORT[1] ?? 'Jan').' '.substr((string) self::MIN_YEAR, -2);
        $shortEnd = (self::MONTHS_SHORT[(int) $rangeEnd->month] ?? (string) $rangeEnd->month).' '.substr((string) $rangeEnd->year, -2);
        $periodCaption = 'Semua data (per bulan, '.$shortStart.'–'.$shortEnd.')';

        return $this->finalizeRows(
            $rows,
            $periodCaption,
            null,
            null,
            '',
            'all',
            'month',
            'Rata-rata bulanan'
        );
    }

    private function buildSingleMonthWeeks(int $year, int $month): array
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();

        /** @var array<string, Carbon> $uniqueMondays key = Y-m-d Senin */
        $uniqueMondays = [];
        for ($d = $monthStart->copy(); $d->lte($monthEnd); $d->addDay()) {
            $mon = $d->copy()->startOfWeek(Carbon::MONDAY);
            $uniqueMondays[$mon->format('Y-m-d')] = $mon;
        }
        ksort($uniqueMondays);

        $rows = [];
        foreach ($uniqueMondays as $mon) {
            $weekEnd = $mon->copy()->endOfWeek(Carbon::SUNDAY);
            $clipStart = $mon->copy()->max($monthStart);
            $clipEnd = $weekEnd->copy()->min($monthEnd);

            $byCategory = $this->aggregateCategoriesBetween(
                $clipStart->toDateString(),
                $clipEnd->toDateString()
            );
            $count = array_sum($byCategory);

            $isoY = $mon->isoWeekYear();
            $label = 'WK '.$mon->isoWeek();
            if ($isoY !== $year) {
                $label .= ' · '.$isoY;
            }

            $rows[] = [
                'label' => $label,
                'count' => $count,
                'by_category' => $byCategory,
                'range_short' => $clipStart->format('d M').' – '.$clipEnd->format('d M'),
                'iso_week_year' => $isoY,
            ];
        }

        $monthLabel = self::MONTHS_ID[$month] ?? (string) $month;
        $periodCaption = $monthLabel.' '.$year;

        return $this->finalizeRows(
            $rows,
            $periodCaption,
            $year,
            $month,
            $monthLabel,
            'month',
            'week',
            'Rata-rata mingguan'
        );
    }

    /**
     * @return array<string, int>
     */
    private function aggregateCategoriesBetween(string $startDate, string $endDate): array
    {
        $base = [];
        foreach (self::DEVIATION_KEYS as $key) {
            $base[$key] = 0;
        }

        $plucked = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $startDate)
            ->where('tanggal_temuan', '<=', $endDate)
            ->pluck('kategori_deviasi');

        foreach ($plucked as $raw) {
            $bucket = KategoriDeviasiBucket::bucket($raw);
            $base[$bucket] = ($base[$bucket] ?? 0) + 1;
        }

        return $base;
    }

    /**
     * @param  list<array{label: string, count: int, range_short: string, iso_week_year: int, by_category?: array<string, int>}>  $rows
     * @return array<string, mixed>
     */
    private function finalizeRows(
        array $rows,
        string $periodCaption,
        ?int $chartYear,
        ?int $chartMonth,
        string $monthLabel,
        string $periodScope,
        string $granularity,
        string $avgLegendLabel
    ): array {
        $counts = array_column($rows, 'count');
        $maxCount = $counts !== [] && max($counts) !== false ? (int) max($counts) : 0;
        $denom = $maxCount > 0 ? $maxCount : 1;
        $avgCount = $counts !== [] ? array_sum($counts) / count($counts) : 0.0;

        foreach ($rows as $k => $r) {
            $c = (int) $r['count'];
            $pct = round(($c / $denom) * 100, 1);
            if ($c > 0 && $pct < 6.0) {
                $pct = 6.0;
            }
            $rows[$k]['bar_height_pct'] = $pct;

            $by = $r['by_category'] ?? [];
            $totalCat = array_sum($by);
            $stackPct = [];
            foreach (self::DEVIATION_KEYS as $key) {
                $n = (int) ($by[$key] ?? 0);
                $stackPct[$key] = $totalCat > 0 ? round(($n / $totalCat) * 100, 3) : 0.0;
            }
            $rows[$k]['category_stack_pct'] = $stackPct;
        }

        $targetLineBottomPct = round(($avgCount / $denom) * 100, 1);

        return [
            'weeks' => $rows,
            'max_count' => $maxCount,
            'avg_count' => round($avgCount, 1),
            'target_line_bottom_pct' => $targetLineBottomPct,
            'period_caption' => $periodCaption,
            'chart_year' => $chartYear,
            'chart_month' => $chartMonth,
            'month_label' => $monthLabel,
            'period_scope' => $periodScope,
            'chart_granularity' => $granularity,
            'avg_legend_label' => $avgLegendLabel,
            'deviation_categories' => $this->deviationCategoriesMeta(),
        ];
    }

    /**
     * @return list<array{key: string, label: string, color: string}>
     */
    private function deviationCategoriesMeta(): array
    {
        $map = [
            'tidak_speak_up_fatigue' => ['label' => 'Tidak Speak Up Fatigue', 'color' => '#2563eb'],
            'blindspot_to_be_concerned' => ['label' => 'Blindspot To Be Concerned Hazards', 'color' => '#7c3aed'],
            'pelanggaran_pspp' => ['label' => 'Pelanggaran PSPP', 'color' => '#059669'],
            'pelanggaran_golden_rules' => ['label' => 'Pelanggaran Golden Rules', 'color' => '#d97706'],
            'insiden' => ['label' => 'Insiden', 'color' => '#dc2626'],
            'lainnya' => ['label' => 'Lainnya', 'color' => '#94a3b8'],
        ];
        $out = [];
        foreach (self::DEVIATION_KEYS as $key) {
            $out[] = [
                'key' => $key,
                'label' => $map[$key]['label'] ?? $key,
                'color' => $map[$key]['color'] ?? '#64748b',
            ];
        }

        return $out;
    }
}
