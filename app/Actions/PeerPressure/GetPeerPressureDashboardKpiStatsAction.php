<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Models\PeerPressurePesertaEdukasi;
use App\Support\PeerPressure\KategoriDeviasiBucket;
use App\Support\PeerPressure\PelaksanaanComplianceEvaluator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Agregasi KPI dashboard Peer Pressure dari MySQL (tanpa N+1).
 * Tanpa argumen: seluruh data. Dengan tahun+bulan: filter tanggal temuan dalam bulan kalender tersebut.
 */
final class GetPeerPressureDashboardKpiStatsAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    /**
     * @return array{
     *   total_cases: int,
     *   total_cases_trend_pct: float|null,
     *   total_cases_trend_label: string,
     *   completion_rate: float,
     *   completion_rate_delta_pp: float|null,
     *   avg_peer_count: float,
     *   avg_duration_minutes: float,
     *   peer_pressure_compliance_pct: float,
     *   peer_pressure_compliance_total: int,
     *   peer_pressure_compliance_comply: int,
     *   pelaksanaan_selesai_count: int,
     *   pelaksanaan_belum_count: int,
     *   pelaksanaan_selesai_pct: float,
     *   pelaksanaan_belum_pct: float,
     *   pelaksanaan_status_kosong_count: int,
     *   pelaksanaan_kelompok_kerja_rows: list<array{kelompok: string, selesai: int, belum: int, total: int, pct_selesai: float}>
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null): array
    {
        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));

            return $this->forCalendarMonth($y, $m);
        }

        return $this->globalStats();
    }

    /**
     * @return array{
     *   total_cases: int,
     *   total_cases_trend_pct: float|null,
     *   total_cases_trend_label: string,
     *   completion_rate: float,
     *   completion_rate_delta_pp: float|null,
     *   avg_peer_count: float,
     *   avg_duration_minutes: float,
     *   peer_pressure_compliance_pct: float,
     *   peer_pressure_compliance_total: int,
     *   peer_pressure_compliance_comply: int,
     *   pelaksanaan_selesai_count: int,
     *   pelaksanaan_belum_count: int,
     *   pelaksanaan_selesai_pct: float,
     *   pelaksanaan_belum_pct: float
     * }
     */
    private function forCalendarMonth(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $startS = $start->toDateString();
        $endS = $end->toDateString();

        $base = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $startS)
            ->where('tanggal_temuan', '<=', $endS);

        $total = (clone $base)->count();

        $closedTotal = (clone $base)->where(function ($q): void {
            $q->whereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%CLOSE%'])
                ->orWhereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%SELESAI%']);
        })->count();

        $completionRate = $total > 0 ? round(($closedTotal / $total) * 100, 1) : 0.0;
        $belumPelaksanaan = max(0, $total - $closedTotal);
        $pctSelesai = $total > 0 ? round(100 * $closedTotal / $total, 1) : 0.0;
        $pctBelum = $total > 0 ? round(100 * $belumPelaksanaan / $total, 1) : 0.0;

        $peerRows = PeerPressurePesertaEdukasi::query()
            ->where('peran', 'peer')
            ->whereHas('kejadian', function ($q) use ($startS, $endS): void {
                $q->where('tanggal_temuan', '>=', $startS)
                    ->where('tanggal_temuan', '<=', $endS);
            })
            ->count();
        $avgPeerCount = $total > 0 ? round($peerRows / $total, 1) : 0.0;

        $avgDur = (clone $base)->avg('durasi_edukasi_menit');
        $avgDurationMinutes = $avgDur !== null ? round((float) $avgDur, 1) : 0.0;

        $prevStart = $start->copy()->subMonth()->startOfMonth();
        $prevEnd = $prevStart->copy()->endOfMonth();
        $prevTotal = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $prevStart->toDateString())
            ->where('tanggal_temuan', '<=', $prevEnd->toDateString())
            ->count();

        $trendPct = null;
        if ($prevTotal > 0) {
            $trendPct = round((($total - $prevTotal) / $prevTotal) * 100, 1);
        } elseif ($total > 0 && $prevTotal === 0) {
            $trendPct = 100.0;
        }

        $trendLabel = 'vs bulan sebelumnya';
        if ($trendPct === null) {
            $trendLabel = $total === 0 && $prevTotal === 0 ? 'Belum ada data di bulan ini' : ($prevTotal === 0 ? 'Tanpa pembanding (bulan lalu 0)' : '—');
        } else {
            $sign = $trendPct > 0 ? '+' : '';
            $trendLabel = $sign . $trendPct . '% ' . $trendLabel;
        }

        $completionDelta = $this->completionRateDeltaMonthVsPreviousMonth($start);

        $compliance = $this->computePelaksanaanCompliance($base);
        $statusKosong = $this->countPelaksanaanStatusKosong($base);
        $kkRows = $this->pelaksanaanKelompokKerjaBreakdown($base);

        return [
            'total_cases' => $total,
            'total_cases_trend_pct' => $trendPct,
            'total_cases_trend_label' => $trendLabel,
            'completion_rate' => $completionRate,
            'completion_rate_delta_pp' => $completionDelta,
            'avg_peer_count' => $avgPeerCount,
            'avg_duration_minutes' => $avgDurationMinutes,
            'pelaksanaan_selesai_count' => $closedTotal,
            'pelaksanaan_belum_count' => $belumPelaksanaan,
            'pelaksanaan_selesai_pct' => $pctSelesai,
            'pelaksanaan_belum_pct' => $pctBelum,
            'pelaksanaan_status_kosong_count' => $statusKosong,
            'pelaksanaan_kelompok_kerja_rows' => $kkRows,
            ...$compliance,
        ];
    }

    private function completionRateDeltaMonthVsPreviousMonth(Carbon $monthStart): ?float
    {
        $startS = $monthStart->copy()->startOfMonth()->toDateString();
        $endS = $monthStart->copy()->endOfMonth()->toDateString();

        $currTotal = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $startS)
            ->where('tanggal_temuan', '<=', $endS)
            ->count();
        if ($currTotal === 0) {
            return null;
        }

        $currClosed = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $startS)
            ->where('tanggal_temuan', '<=', $endS)
            ->where(function ($q): void {
                $q->whereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%CLOSE%'])
                    ->orWhereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%SELESAI%']);
            })
            ->count();

        $prevStart = $monthStart->copy()->subMonth()->startOfMonth();
        $prevEnd = $prevStart->copy()->endOfMonth();
        $prevTotal = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $prevStart->toDateString())
            ->where('tanggal_temuan', '<=', $prevEnd->toDateString())
            ->count();
        if ($prevTotal === 0) {
            return null;
        }

        $prevClosed = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $prevStart->toDateString())
            ->where('tanggal_temuan', '<=', $prevEnd->toDateString())
            ->where(function ($q): void {
                $q->whereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%CLOSE%'])
                    ->orWhereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%SELESAI%']);
            })
            ->count();

        $rateRecent = ($currClosed / $currTotal) * 100;
        $ratePrev = ($prevClosed / $prevTotal) * 100;

        return round($rateRecent - $ratePrev, 1);
    }

    /**
     * @return array{
     *   total_cases: int,
     *   total_cases_trend_pct: float|null,
     *   total_cases_trend_label: string,
     *   completion_rate: float,
     *   completion_rate_delta_pp: float|null,
     *   avg_peer_count: float,
     *   avg_duration_minutes: float,
     *   peer_pressure_compliance_pct: float,
     *   peer_pressure_compliance_total: int,
     *   peer_pressure_compliance_comply: int,
     *   pelaksanaan_selesai_count: int,
     *   pelaksanaan_belum_count: int,
     *   pelaksanaan_selesai_pct: float,
     *   pelaksanaan_belum_pct: float
     * }
     */
    private function globalStats(): array
    {
        $total = PeerPressureKejadianEdukasi::query()->count();

        $closedTotal = PeerPressureKejadianEdukasi::query()
            ->where(function ($q): void {
                $q->whereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%CLOSE%'])
                    ->orWhereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%SELESAI%']);
            })
            ->count();

        $completionRate = $total > 0 ? round(($closedTotal / $total) * 100, 1) : 0.0;
        $belumPelaksanaan = max(0, $total - $closedTotal);
        $pctSelesai = $total > 0 ? round(100 * $closedTotal / $total, 1) : 0.0;
        $pctBelum = $total > 0 ? round(100 * $belumPelaksanaan / $total, 1) : 0.0;

        $peerRows = PeerPressurePesertaEdukasi::query()->where('peran', 'peer')->count();
        $avgPeerCount = $total > 0 ? round($peerRows / $total, 1) : 0.0;

        $avgDur = PeerPressureKejadianEdukasi::query()->avg('durasi_edukasi_menit');
        $avgDurationMinutes = $avgDur !== null ? round((float) $avgDur, 1) : 0.0;

        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30)->startOfDay()->toDateString();
        $last30Count = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $last30Start)
            ->count();

        $prev30Count = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $now->copy()->subDays(60)->startOfDay()->toDateString())
            ->where('tanggal_temuan', '<', $last30Start)
            ->count();

        $trendPct = null;
        if ($prev30Count > 0) {
            $trendPct = round((($last30Count - $prev30Count) / $prev30Count) * 100, 1);
        } elseif ($last30Count > 0 && $prev30Count === 0) {
            $trendPct = 100.0;
        }

        $trendLabel = 'vs 30 hari sebelumnya';
        if ($trendPct === null) {
            $trendLabel = $total === 0 ? 'Belum ada data' : 'Tanpa pembanding';
        } else {
            $sign = $trendPct > 0 ? '+' : '';
            $trendLabel = $sign . $trendPct . '% ' . $trendLabel;
        }

        $completionDelta = $this->completionRateDeltaLast30VsPrev30();

        $baseGlobal = PeerPressureKejadianEdukasi::query();
        $compliance = $this->computePelaksanaanCompliance($baseGlobal);
        $statusKosong = $this->countPelaksanaanStatusKosong($baseGlobal);
        $kkRows = $this->pelaksanaanKelompokKerjaBreakdown($baseGlobal);

        return [
            'total_cases' => $total,
            'total_cases_trend_pct' => $trendPct,
            'total_cases_trend_label' => $trendLabel,
            'completion_rate' => $completionRate,
            'completion_rate_delta_pp' => $completionDelta,
            'avg_peer_count' => $avgPeerCount,
            'avg_duration_minutes' => $avgDurationMinutes,
            'pelaksanaan_selesai_count' => $closedTotal,
            'pelaksanaan_belum_count' => $belumPelaksanaan,
            'pelaksanaan_selesai_pct' => $pctSelesai,
            'pelaksanaan_belum_pct' => $pctBelum,
            'pelaksanaan_status_kosong_count' => $statusKosong,
            'pelaksanaan_kelompok_kerja_rows' => $kkRows,
            ...$compliance,
        ];
    }

    private function countPelaksanaanStatusKosong(Builder $base): int
    {
        return (clone $base)
            ->whereRaw('TRIM(COALESCE(status_pelaksanaan_edukasi, \'\')) = \'\'')
            ->count();
    }

    /**
     * Agregasi per jenis kelompok kerja: selesai (CLOSED/SELESAI) vs belum, untuk modal pelaksanaan.
     *
     * @return list<array{kelompok: string, selesai: int, belum: int, total: int, pct_selesai: float}>
     */
    private function pelaksanaanKelompokKerjaBreakdown(Builder $base): array
    {
        $labelExpr = "COALESCE(NULLIF(TRIM(jenis_kelompok_kerja), ''), '(Tidak diisi)')";
        $selesaiWhen = '(UPPER(TRIM(COALESCE(status_pelaksanaan_edukasi, \'\'))) LIKE \'%CLOSE%\' OR UPPER(TRIM(COALESCE(status_pelaksanaan_edukasi, \'\'))) LIKE \'%SELESAI%\')';

        $rows = (clone $base)
            ->selectRaw($labelExpr.' as kelompok')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN '.$selesaiWhen.' THEN 1 ELSE 0 END) as selesai')
            ->groupBy(DB::raw($labelExpr))
            ->orderByDesc('total')
            ->limit(15)
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $total = (int) $r->total;
            $selesai = (int) $r->selesai;
            $belum = max(0, $total - $selesai);
            $pct = $total > 0 ? round(100 * $selesai / $total, 1) : 0.0;
            $out[] = [
                'kelompok' => (string) $r->kelompok,
                'selesai' => $selesai,
                'belum' => $belum,
                'total' => $total,
                'pct_selesai' => $pct,
            ];
        }

        return $out;
    }

    /**
     * Comply per kejadian (0% / 100%): Fatigue & Blindspot = pelaksanaan selesai.
     * PSPP, Golden Rules, Insiden = selesai dan terhubung BeRecord (id_berecord terisi).
     * Hanya lima kategori deviasi terlacak; "Lainnya" tidak masuk pembilang.
     */
    private function computePelaksanaanCompliance(Builder $query): array
    {
        $rows = (clone $query)->get(['kategori_deviasi', 'status_pelaksanaan_edukasi', 'id_berecord']);

        $tracked = array_flip(KategoriDeviasiBucket::trackedComplianceBuckets());
        $denom = 0;
        $comply = 0;

        foreach ($rows as $r) {
            $bucket = KategoriDeviasiBucket::bucket($r->kategori_deviasi);
            if (! isset($tracked[$bucket])) {
                continue;
            }

            $denom++;
            $ev = PelaksanaanComplianceEvaluator::evaluate(
                $bucket,
                $r->status_pelaksanaan_edukasi,
                $r->id_berecord
            );
            if ($ev['comply']) {
                $comply++;
            }
        }

        $pct = $denom > 0 ? round(100 * $comply / $denom, 1) : 0.0;

        return [
            'peer_pressure_compliance_pct' => $pct,
            'peer_pressure_compliance_total' => $denom,
            'peer_pressure_compliance_comply' => $comply,
        ];
    }

    private function completionRateDeltaLast30VsPrev30(): ?float
    {
        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30)->startOfDay()->toDateString();

        $last30Total = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $last30Start)
            ->count();
        if ($last30Total === 0) {
            return null;
        }

        $last30Closed = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $last30Start)
            ->where(function ($q): void {
                $q->whereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%CLOSE%'])
                    ->orWhereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%SELESAI%']);
            })
            ->count();

        $prev30Total = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $now->copy()->subDays(60)->startOfDay()->toDateString())
            ->where('tanggal_temuan', '<', $last30Start)
            ->count();

        if ($prev30Total === 0) {
            return null;
        }

        $prev30Closed = PeerPressureKejadianEdukasi::query()
            ->where('tanggal_temuan', '>=', $now->copy()->subDays(60)->startOfDay()->toDateString())
            ->where('tanggal_temuan', '<', $last30Start)
            ->where(function ($q): void {
                $q->whereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%CLOSE%'])
                    ->orWhereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%SELESAI%']);
            })
            ->count();

        $rateRecent = ($last30Closed / $last30Total) * 100;
        $ratePrev = ($prev30Closed / $prev30Total) * 100;

        return round($rateRecent - $ratePrev, 1);
    }
}
