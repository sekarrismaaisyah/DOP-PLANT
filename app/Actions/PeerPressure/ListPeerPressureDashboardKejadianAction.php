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
    public function __invoke(?string $q = null): LengthAwarePaginator
    {
        $query = PeerPressureKejadianEdukasi::query()
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
                'peserta' => static function ($rel): void {
                    $rel->select(['id', 'kejadian_edukasi_id', 'sid', 'nama', 'peran', 'urutan'])
                        ->orderBy('urutan');
                },
            ]);

        $q = $q !== null ? trim($q) : '';
        if ($q !== '') {
            $query->where(function ($sub) use ($q): void {
                $sub->where('lokasi_temuan', 'like', '%' . $q . '%')
                    ->orWhere('kategori_deviasi', 'like', '%' . $q . '%')
                    ->orWhere('departemen', 'like', '%' . $q . '%')
                    ->orWhere('aktivitas_pekerjaan', 'like', '%' . $q . '%')
                    ->orWhere('pemimpin_edukasi', 'like', '%' . $q . '%')
                    ->orWhere('status_pelaksanaan_edukasi', 'like', '%' . $q . '%')
                    ->orWhere('perusahaan', 'like', '%' . $q . '%')
                    ->orWhere('kronologi_temuan', 'like', '%' . $q . '%')
                    ->orWhereHas('peserta', function ($p) use ($q): void {
                        $p->where('sid', 'like', '%' . $q . '%')
                            ->orWhere('nama', 'like', '%' . $q . '%');
                    });
            });
        }

        return $query
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('jam_temuan')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
    }
}
