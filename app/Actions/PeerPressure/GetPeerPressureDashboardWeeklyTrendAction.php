<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use Carbon\Carbon;

/**
 * Chart trend: per minggu ISO dalam satu bulan, atau agregasi per bulan untuk seluruh rentang 2025–2026.
 */
final class GetPeerPressureDashboardWeeklyTrendAction
{
    public const MIN_YEAR = 2025;

    public const MAX_YEAR = 2026;

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
     *   weeks: list<array{label: string, count: int, bar_height_pct: float, range_short: string, iso_week_year: int}>,
     *   max_count: int,
     *   avg_count: float,
     *   target_line_bottom_pct: float,
     *   period_caption: string,
     *   chart_year: int|null,
     *   chart_month: int|null,
     *   month_label: string,
     *   period_scope: 'all'|'month',
     *   chart_granularity: 'week'|'month',
     *   avg_legend_label: string
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

    /**
     * @return array{
     *   weeks: list<array{label: string, count: int, bar_height_pct: float, range_short: string, iso_week_year: int}>,
     *   max_count: int,
     *   avg_count: float,
     *   target_line_bottom_pct: float,
     *   period_caption: string,
     *   chart_year: int|null,
     *   chart_month: int|null,
     *   month_label: string,
     *   period_scope: 'all',
     *   chart_granularity: 'month',
     *   avg_legend_label: string
     * }
     */
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

            $count = PeerPressureKejadianEdukasi::query()
                ->where('tanggal_temuan', '>=', $monthStart->toDateString())
                ->where('tanggal_temuan', '<=', $monthEnd->toDateString())
                ->count();

            $shortY = substr((string) $y, -2);
            $label = (self::MONTHS_SHORT[$m] ?? (string) $m) . ' ' . $shortY;

            $rows[] = [
                'label' => $label,
                'count' => $count,
                'range_short' => $monthStart->format('d M') . ' – ' . $monthEnd->format('d M Y'),
                'iso_week_year' => $y,
            ];

            $cursor->addMonth();
        }

        $shortStart = (self::MONTHS_SHORT[1] ?? 'Jan') . ' ' . substr((string) self::MIN_YEAR, -2);
        $shortEnd = (self::MONTHS_SHORT[(int) $rangeEnd->month] ?? (string) $rangeEnd->month) . ' ' . substr((string) $rangeEnd->year, -2);
        $periodCaption = 'Semua data (per bulan, ' . $shortStart . '–' . $shortEnd . ')';

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

    /**
     * @return array{
     *   weeks: list<array{label: string, count: int, bar_height_pct: float, range_short: string, iso_week_year: int}>,
     *   max_count: int,
     *   avg_count: float,
     *   target_line_bottom_pct: float,
     *   period_caption: string,
     *   chart_year: int|null,
     *   chart_month: int|null,
     *   month_label: string,
     *   period_scope: 'month',
     *   chart_granularity: 'week',
     *   avg_legend_label: string
     * }
     */
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

            $count = PeerPressureKejadianEdukasi::query()
                ->where('tanggal_temuan', '>=', $clipStart->toDateString())
                ->where('tanggal_temuan', '<=', $clipEnd->toDateString())
                ->count();

            $isoY = $mon->isoWeekYear();
            $label = 'WK ' . $mon->isoWeek();
            if ($isoY !== $year) {
                $label .= ' · ' . $isoY;
            }

            $rows[] = [
                'label' => $label,
                'count' => $count,
                'range_short' => $clipStart->format('d M') . ' – ' . $clipEnd->format('d M'),
                'iso_week_year' => $isoY,
            ];
        }

        $monthLabel = self::MONTHS_ID[$month] ?? (string) $month;
        $periodCaption = $monthLabel . ' ' . $year;

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
     * @param list<array{label: string, count: int, range_short: string, iso_week_year: int}> $rows
     *
     * @return array{
     *   weeks: list<array{label: string, count: int, bar_height_pct: float, range_short: string, iso_week_year: int}>,
     *   max_count: int,
     *   avg_count: float,
     *   target_line_bottom_pct: float,
     *   period_caption: string,
     *   chart_year: int|null,
     *   chart_month: int|null,
     *   month_label: string,
     *   period_scope: 'all'|'month',
     *   chart_granularity: 'week'|'month',
     *   avg_legend_label: string
     * }
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
            $c = $r['count'];
            $pct = round(($c / $denom) * 100, 1);
            if ($c > 0 && $pct < 6.0) {
                $pct = 6.0;
            }
            $rows[$k]['bar_height_pct'] = $pct;
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
        ];
    }
}
