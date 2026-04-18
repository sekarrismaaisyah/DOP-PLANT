<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Models\SpeakUpFatigue;
use App\Models\ValidasiTbc;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Ringkasan tiga kartu pada modal "Total Deviasi Pelanggaran":
 * BeRecord (PSPP + Golden Rules dari kejadian), Validasi TBC (blindspot BC terisi), Speak Up Fatigue (baris = tidak speak up).
 */
final class GetPeerPressureDeviationModalBreakdownAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    /**
     * @return array{
     *   berecord_pspp_gr_total: int,
     *   validasi_tbc_blindspot_terisi_total: int,
     *   speak_up_fatigue_tidak_speak_total: int
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null): array
    {
        $base = $this->scopedKejadianQuery($year, $month);

        $berecordTotal = (clone $base)->where(function ($q): void {
            $q->whereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%pspp%'])
                ->orWhereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%golden%']);
        })->count();

        $tbcQuery = ValidasiTbc::query()
            ->whereRaw('LENGTH(TRIM(COALESCE(blindspot_terlapor_bc, \'\'))) > 0');

        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $tbcQuery->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        }

        $tbcTotal = $tbcQuery->count();

        $fatigueQuery = SpeakUpFatigue::query();
        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $fatigueQuery->where('tanggal', '>=', $start->toDateString())
                ->where('tanggal', '<=', $end->toDateString());
        }

        $fatigueTotal = $fatigueQuery->count();

        return [
            'berecord_pspp_gr_total' => $berecordTotal,
            'validasi_tbc_blindspot_terisi_total' => $tbcTotal,
            'speak_up_fatigue_tidak_speak_total' => $fatigueTotal,
        ];
    }

    private function scopedKejadianQuery(?int $year, ?int $month): Builder
    {
        $q = PeerPressureKejadianEdukasi::query();
        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $q->where('tanggal_temuan', '>=', $start->toDateString())
                ->where('tanggal_temuan', '<=', $end->toDateString());
        }

        return $q;
    }
}
