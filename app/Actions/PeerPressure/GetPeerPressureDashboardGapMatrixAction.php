<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Support\PeerPressure\KategoriDeviasiBucket;
use App\Support\PeerPressure\PelaksanaanComplianceEvaluator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Matriks gap: sumbu X = tingkat pelaksanaan (peer pressure selesai),
 * sumbu Y = tingkat kepatuhan (comply) per jenis kelompok kerja — sama periode dengan KPI dashboard.
 */
final class GetPeerPressureDashboardGapMatrixAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    /** Ambang "tinggi/rendah" untuk kuadran (selaras label produk). */
    private const THRESHOLD_EXECUTION = 50.0;

    private const THRESHOLD_COMPLIANCE = 50.0;

    private const TOP_GROUPS = 15;

    /**
     * @return array{
     *   period_scope: string,
     *   threshold_execution: float,
     *   threshold_compliance: float,
     *   points: list<array{
     *     kelompok: string,
     *     x: float,
     *     y: float,
     *     total: int,
     *     selesai: int,
     *     tracked_total: int,
     *     comply: int,
     *     quadrant: string,
     *     quadrant_key: string,
     *     quadrant_label: string,
     *     color: string
     *   }>,
     *   quadrants: list<array{key: string, label: string, emoji: string, color: string, hint: string}>
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null): array
    {
        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $base = $this->baseForMonth($y, $m);

            return $this->buildPayload($base, 'month');
        }

        $base = PeerPressureKejadianEdukasi::query();

        return $this->buildPayload($base, 'all');
    }

    private function baseForMonth(int $year, int $month): Builder
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $startS = $start->toDateString();
        $endS = $end->toDateString();

        return PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $startS)
            ->where('tanggal_temuan', '<=', $endS);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(Builder $base, string $periodScope): array
    {
        $rows = (clone $base)->get([
            'jenis_kelompok_kerja',
            'kategori_deviasi',
            'status_pelaksanaan_edukasi',
            'id_berecord',
        ]);

        $byKel = [];
        foreach ($rows as $r) {
            $k = trim((string) $r->jenis_kelompok_kerja);
            $label = $k === '' ? '(Tidak diisi)' : $k;
            if (! isset($byKel[$label])) {
                $byKel[$label] = [
                    'total' => 0,
                    'selesai' => 0,
                    'tracked' => 0,
                    'comply' => 0,
                ];
            }
            $byKel[$label]['total']++;

            if (PelaksanaanComplianceEvaluator::isPelaksanaanClosed($r->status_pelaksanaan_edukasi)) {
                $byKel[$label]['selesai']++;
            }

            $bucket = KategoriDeviasiBucket::bucket($r->kategori_deviasi);
            $tracked = array_flip(KategoriDeviasiBucket::trackedComplianceBuckets());
            if (! isset($tracked[$bucket])) {
                continue;
            }

            $byKel[$label]['tracked']++;
            $ev = PelaksanaanComplianceEvaluator::evaluate(
                $bucket,
                $r->status_pelaksanaan_edukasi,
                $r->id_berecord
            );
            if ($ev['comply']) {
                $byKel[$label]['comply']++;
            }
        }

        uasort($byKel, static fn (array $a, array $b): int => $b['total'] <=> $a['total']);
        $top = array_slice($byKel, 0, self::TOP_GROUPS, true);

        $points = [];
        foreach ($top as $kelompok => $agg) {
            $total = $agg['total'];
            $selesai = $agg['selesai'];
            $tracked = $agg['tracked'];
            $comply = $agg['comply'];

            $x = $total > 0 ? round(100 * $selesai / $total, 1) : 0.0;
            $y = $tracked > 0 ? round(100 * $comply / $tracked, 1) : 0.0;

            $qk = $this->quadrantKey($x, $y);
            $meta = $this->quadrantMeta($qk);

            $points[] = [
                'kelompok' => $kelompok,
                'x' => $x,
                'y' => $y,
                'total' => $total,
                'selesai' => $selesai,
                'tracked_total' => $tracked,
                'comply' => $comply,
                'quadrant' => $meta['key'],
                'quadrant_key' => $qk,
                'quadrant_label' => $meta['label'],
                'color' => $meta['color'],
            ];
        }

        return [
            'period_scope' => $periodScope,
            'threshold_execution' => self::THRESHOLD_EXECUTION,
            'threshold_compliance' => self::THRESHOLD_COMPLIANCE,
            'points' => $points,
            'quadrants' => $this->quadrantLegend(),
        ];
    }

    private function quadrantKey(float $x, float $y): string
    {
        $highX = $x >= self::THRESHOLD_EXECUTION;
        $highY = $y >= self::THRESHOLD_COMPLIANCE;

        if ($highX && $highY) {
            return 'hh';
        }
        if ($highX && ! $highY) {
            return 'hl';
        }
        if (! $highX && ! $highY) {
            return 'll';
        }

        return 'lh';
    }

    /**
     * @return array{key: string, label: string, color: string}
     */
    private function quadrantMeta(string $qk): array
    {
        return match ($qk) {
            'hh' => [
                'key' => 'hh',
                'label' => 'Tinggi pelaksanaan & tinggi kepatuhan',
                'color' => '#16a34a',
            ],
            'hl' => [
                'key' => 'hl',
                'label' => 'Tinggi pelaksanaan & rendah kepatuhan',
                'color' => '#ca8a04',
            ],
            'll' => [
                'key' => 'll',
                'label' => 'Rendah pelaksanaan & rendah kepatuhan',
                'color' => '#dc2626',
            ],
            'lh' => [
                'key' => 'lh',
                'label' => 'Rendah pelaksanaan & tinggi kepatuhan',
                'color' => '#2563eb',
            ],
            default => [
                'key' => 'unknown',
                'label' => '—',
                'color' => '#64748b',
            ],
        };
    }

    /**
     * @return list<array{key: string, label: string, emoji: string, color: string, hint: string}>
     */
    private function quadrantLegend(): array
    {
        return [
            [
                'key' => 'hh',
                'emoji' => '🟢',
                'label' => 'Tinggi pelaksanaan & tinggi kepatuhan',
                'color' => '#16a34a',
                'hint' => 'Eksekusi dan aturan selaras.',
            ],
            [
                'key' => 'hl',
                'emoji' => '🟡',
                'label' => 'Tinggi pelaksanaan & rendah kepatuhan',
                'color' => '#ca8a04',
                'hint' => 'Butuh training / klarifikasi SOP.',
            ],
            [
                'key' => 'll',
                'emoji' => '🔴',
                'label' => 'Rendah pelaksanaan & rendah kepatuhan',
                'color' => '#dc2626',
                'hint' => 'Butuh enforcement / prioritas penyelesaian.',
            ],
            [
                'key' => 'lh',
                'emoji' => '🔵',
                'label' => 'Rendah pelaksanaan & tinggi kepatuhan',
                'color' => '#2563eb',
                'hint' => 'Volume rendah; kepatuhan tinggi pada sampel kecil.',
            ],
        ];
    }
}
