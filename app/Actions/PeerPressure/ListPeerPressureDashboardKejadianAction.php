<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Mengambil daftar kejadian Peer Pressure untuk dashboard (eager load peserta, kolom terbatas).
 */
final class ListPeerPressureDashboardKejadianAction
{
    public function __invoke(): LengthAwarePaginator
    {
        return PeerPressureKejadianEdukasi::query()
            ->select([
                'id',
                'tanggal_temuan',
                'jam_temuan',
                'lokasi_temuan',
                'kategori_deviasi',
                'departemen',
                'aktivitas_pekerjaan',
                'pemimpin_edukasi',
                'evidence_url',
                'durasi_edukasi_menit',
                'status_pelaksanaan_edukasi',
            ])
            ->with([
                'peserta' => static function ($q): void {
                    $q->select(['id', 'kejadian_edukasi_id', 'sid', 'nama', 'peran', 'urutan'])
                        ->orderBy('urutan');
                },
            ])
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('jam_temuan')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }
}
