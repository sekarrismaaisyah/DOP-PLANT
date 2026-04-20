<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Support\PeerPressure\PelaksanaanComplianceEvaluator;
use Carbon\Carbon;

/**
 * Agregat % pelaksanaan selesai (CLOSED/SELESAI) vs tidak, per perusahaan (per bulan atau seluruh data).
 */
final class GetPeerPressurePerusahaanPelaksanaanHeatmapAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    /** Maks baris perusahaan (diurutkan volume). */
    private const MAX_COMPANIES = 30;

    /**
     * @return array{
     *   period_scope: string,
     *   period_label: string,
     *   companies: list<string>,
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

        return $this->forAllData();
    }

    /**
     * @return array<string, mixed>
     */
    private function forMonth(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        return $this->buildForPeriod($start, $end, 'month', sprintf('%s %d', $start->translatedFormat('F'), $year));
    }

    /**
     * Seluruh baris kejadian (tanpa filter tanggal temuan).
     *
     * @return array<string, mixed>
     */
    private function forAllData(): array
    {
        $rows = PeerPressureKejadianEdukasi::query()
            ->get(['perusahaan', 'status_pelaksanaan_edukasi']);

        return $this->aggregateRows($rows, 'all', 'Semua data');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildForPeriod(Carbon $start, Carbon $end, string $periodScope, string $periodLabel): array
    {
        $startS = $start->toDateString();
        $endS = $end->toDateString();

        $rows = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $startS)
            ->where('tanggal_temuan', '<=', $endS)
            ->get(['perusahaan', 'status_pelaksanaan_edukasi']);

        return $this->aggregateRows($rows, $periodScope, $periodLabel);
    }

    /**
     * @param \Illuminate\Support\Collection<int, PeerPressureKejadianEdukasi> $rows
     *
     * @return array<string, mixed>
     */
    private function aggregateRows($rows, string $periodScope, string $periodLabel): array
    {
        /** @var array<string, array{total: int, selesai: int}> $acc */
        $acc = [];

        foreach ($rows as $r) {
            $p = trim((string) $r->perusahaan);
            $company = $p === '' ? '(Tidak diisi)' : $p;
            if (! isset($acc[$company])) {
                $acc[$company] = ['total' => 0, 'selesai' => 0];
            }
            $acc[$company]['total']++;
            if (PelaksanaanComplianceEvaluator::isPelaksanaanClosed($r->status_pelaksanaan_edukasi)) {
                $acc[$company]['selesai']++;
            }
        }

        $companyTotals = [];
        foreach ($acc as $c => $v) {
            $companyTotals[$c] = $v['total'];
        }
        arsort($companyTotals);
        $topCompanies = array_slice(array_keys($companyTotals), 0, self::MAX_COMPANIES);

        $grandRow = [];
        foreach ($topCompanies as $company) {
            $cell = $acc[$company] ?? null;
            if ($cell === null || $cell['total'] === 0) {
                $grandRow[$company] = null;

                continue;
            }
            $total = $cell['total'];
            $selesai = $cell['selesai'];
            $belum = $total - $selesai;
            $grandRow[$company] = [
                'pct' => round(100 * $selesai / $total, 1),
                'pct_belum' => round(100 * $belum / $total, 1),
                'total' => $total,
                'selesai' => $selesai,
            ];
        }

        return [
            'period_scope' => $periodScope,
            'period_label' => $periodLabel,
            'companies' => $topCompanies,
            'grand_row' => $grandRow,
        ];
    }
}
