<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Services\PeerPressure\PeerPressurePelaksanaanBaselineService;

/**
 * Agregat % pelaksanaan terlaksana vs belum per site (dapat di-expand ke perusahaan)
 * berdasarkan baseline (BeRecord CH + validasi TBC + fatigue), hanya kontraktor terpilih.
 */
final class GetPeerPressurePerusahaanPelaksanaanHeatmapAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    public function __construct(
        private readonly PeerPressurePelaksanaanBaselineService $pelaksanaanBaseline,
    ) {}

    /**
     * @return array{
     *   period_scope: string,
     *   period_label: string,
     *   baseline_scope?: bool,
     *   contractor_scope?: list<string>,
     *   sites: list<array{
     *     site: string,
     *     site_key: string,
     *     total: int,
     *     selesai: int,
     *     pct: float,
     *     pct_belum: float,
     *     companies: list<array{company: string, total: int, selesai: int, pct: float, pct_belum: float}>
     *   }>
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null): array
    {
        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));

            return $this->pelaksanaanBaseline->perusahaanHeatmap($y, $m);
        }

        return $this->pelaksanaanBaseline->perusahaanHeatmap(null, null);
    }
}
