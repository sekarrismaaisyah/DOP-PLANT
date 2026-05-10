<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Services\PeerPressure\PeerPressureBerecordNitipService;
use App\Services\PeerPressure\PeerPressureKaryawanNitipService;
use Illuminate\Support\Str;

/**
 * Mengambil detail satu kejadian + peserta untuk modal dashboard (respons JSON).
 */
final class GetPeerPressureKejadianDetailForDashboardAction
{
    public function __construct(
        private readonly PeerPressureBerecordNitipService $peerPressureBerecordNitip,
        private readonly PeerPressureKaryawanNitipService $peerPressureKaryawanNitip
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function __invoke(int $id): array
    {
        $k = PeerPressureKejadianEdukasi::query()
            ->with([
                'peserta' => static function ($q): void {
                    $q->orderBy('urutan');
                },
            ])
            ->findOrFail($id);

        $sidsForFoto = $k->peserta->map(static fn ($p) => $p->sid)->all();
        $fotoBySidLower = $this->peerPressureKaryawanNitip->fotoUrlsByKodeSids($sidsForFoto);

        $peserta = $k->peserta->map(static function ($p) use ($fotoBySidLower): array {
            $sidKey = Str::lower(trim((string) $p->sid));

            return [
                'id' => $p->id,
                'sid' => $p->sid,
                'nama' => $p->nama,
                'peran' => $p->peran,
                'urutan' => $p->urutan,
                'initials' => $p->initials(),
                'foto_url' => $fotoBySidLower[$sidKey] ?? null,
            ];
        })->values()->all();

        $pelanggar = $k->peserta->firstWhere('peran', 'pelanggar');
        $pelanggarBerecord = null;
        if ($pelanggar !== null) {
            $sidRaw = trim((string) $pelanggar->sid);
            if ($sidRaw !== '' && $sidRaw !== '-') {
                $pelanggarBerecord = $this->peerPressureBerecordNitip->findLatestByKodeSid($sidRaw);
            }
        }

        return [
            'id' => $k->id,
            'formatted_temuan' => $k->formattedTemuanDatetime(),
            'formatted_edukasi' => $k->formattedEdukasiDatetime(),
            'kelompok_lokasi_temuan' => $k->kelompok_lokasi_temuan,
            'lokasi_temuan' => $k->lokasi_temuan,
            'kelompok_lokasi_edukasi' => $k->kelompok_lokasi_edukasi,
            'lokasi_edukasi' => $k->lokasi_edukasi,
            'perusahaan' => $k->perusahaan,
            'site' => $k->site,
            'tasklist_temuan' => $k->tasklist_temuan,
            'kronologi_temuan' => $k->kronologi_temuan,
            'kategori_deviasi' => $k->kategori_deviasi,
            'pemimpin_edukasi' => $k->pemimpin_edukasi,
            'id_berecord' => $k->id_berecord,
            'jenis_kelompok_kerja' => $k->jenis_kelompok_kerja,
            'kelompok_aktivitas_pekerjaan' => $k->kelompok_aktivitas_pekerjaan,
            'aktivitas_pekerjaan' => $k->aktivitas_pekerjaan,
            'departemen' => $k->departemen,
            'evidence_url' => $k->evidence_url,
            'durasi_edukasi_menit' => $k->durasi_edukasi_menit,
            'status_pelaksanaan_edukasi' => $k->status_pelaksanaan_edukasi,
            'status_badge' => $k->dashboardStatusBadge(),
            'peserta' => $peserta,
            'pelanggar_berecord' => $pelanggarBerecord,
        ];
    }
}
