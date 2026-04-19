<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Support\PeerPressure\PelaksanaanComplianceEvaluator;
use Carbon\Carbon;

/**
 * Heatmap % pelaksanaan selesai (CLOSED/SELESAI) per perusahaan × tanggal temuan.
 */
final class GetPeerPressurePerusahaanPelaksanaanHeatmapAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    /** Maks baris perusahaan (diurutkan volume). */
    private const MAX_COMPANIES = 30;

    /** Mode "seluruh data": jumlah hari ke belakang (kolom). */
    private const ALL_MODE_DAYS = 31;

    /**
     * @return array{
     *   period_scope: string,
     *   period_label: string,
     *   days: list<array{key: string, label: string, d: int, m: int, y: int}>,
     *   companies: list<string>,
     *   cells: array<string, array<string, array{pct: float, pct_belum: float, total: int, selesai: int}|null>> ,
     *   grand_row: array<string, array{pct: float, pct_belum: float, total: int, selesai: int}|null>
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null): array
    {
        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));

            return $this->forMonth($y, $m);
        }

        return $this->forRollingWindow();
    }

    /**
     * @return array<string, mixed>
     */
    private function forMonth(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        return $this->buildMatrix($start, $end, 'month', sprintf('%s %d', $start->translatedFormat('F'), $year));
    }

    /**
     * @return array<string, mixed>
     */
    private function forRollingWindow(): array
    {
        $end = Carbon::now()->startOfDay();
        $start = $end->copy()->subDays(self::ALL_MODE_DAYS - 1);

        return $this->buildMatrix(
            $start,
            $end,
            'all',
            sprintf('%s – %s (rolling %d hari)', $start->format('d/m/Y'), $end->format('d/m/Y'), self::ALL_MODE_DAYS)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMatrix(Carbon $start, Carbon $end, string $periodScope, string $periodLabel): array
    {
        $startS = $start->toDateString();
        $endS = $end->toDateString();

        $rows = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $startS)
            ->where('tanggal_temuan', '<=', $endS)
            ->get(['perusahaan', 'tanggal_temuan', 'status_pelaksanaan_edukasi']);

        /** @var array<string, array<string, array{total: int, selesai: int}>> $acc */
        $acc = [];
        $companyTotals = [];

        foreach ($rows as $r) {
            $p = trim((string) $r->perusahaan);
            $company = $p === '' ? '(Tidak diisi)' : $p;
            $day = Carbon::parse($r->tanggal_temuan)->toDateString();

            if (! isset($acc[$company][$day])) {
                $acc[$company][$day] = ['total' => 0, 'selesai' => 0];
            }
            $acc[$company][$day]['total']++;
            if (PelaksanaanComplianceEvaluator::isPelaksanaanClosed($r->status_pelaksanaan_edukasi)) {
                $acc[$company][$day]['selesai']++;
            }
            $companyTotals[$company] = ($companyTotals[$company] ?? 0) + 1;
        }

        arsort($companyTotals);
        $topCompanies = array_slice(array_keys($companyTotals), 0, self::MAX_COMPANIES);

        $dayList = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dayList[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $daysOut = [];
        foreach ($dayList as $ds) {
            $c = Carbon::parse($ds);
            $daysOut[] = [
                'key' => $ds,
                'label' => $c->format('d/m'),
                'd' => (int) $c->format('d'),
                'm' => (int) $c->format('m'),
                'y' => (int) $c->format('Y'),
            ];
        }

        $cells = [];
        $grandRow = [];

        foreach ($topCompanies as $company) {
            $cells[$company] = [];
            $sumT = 0;
            $sumS = 0;
            foreach ($dayList as $ds) {
                $cell = $acc[$company][$ds] ?? null;
                if ($cell === null || $cell['total'] === 0) {
                    $cells[$company][$ds] = null;
                    continue;
                }
                $total = $cell['total'];
                $selesai = $cell['selesai'];
                $sumT += $total;
                $sumS += $selesai;
                $belum = $total - $selesai;
                $pct = round(100 * $selesai / $total, 1);
                $pctBelum = round(100 * $belum / $total, 1);
                $cells[$company][$ds] = [
                    'pct' => $pct,
                    'pct_belum' => $pctBelum,
                    'total' => $total,
                    'selesai' => $selesai,
                ];
            }
            $grandRow[$company] = $sumT > 0
                ? [
                    'pct' => round(100 * $sumS / $sumT, 1),
                    'pct_belum' => round(100 * ($sumT - $sumS) / $sumT, 1),
                    'total' => $sumT,
                    'selesai' => $sumS,
                ]
                : null;
        }

        return [
            'period_scope' => $periodScope,
            'period_label' => $periodLabel,
            'days' => $daysOut,
            'companies' => $topCompanies,
            'cells' => $cells,
            'grand_row' => $grandRow,
        ];
    }
}
