<?php

declare(strict_types=1);

namespace App\Services\SistemRoster;

use App\Models\DailyOperationPlan;
use App\Models\RosterPlanning;
use Carbon\Carbon;

/**
 * Menyelaraskan satu baris DOP ke roster_plannings — logika sama dengan GeneratePlanningJob::generateFromDOP.
 */
final class RosterPlanningDopSyncService
{
    /**
     * Upsert baris planning untuk DOP ini.
     *
     * @return bool true jika baris roster baru dibuat, false jika sudah ada (update)
     */
    public function syncFromModel(DailyOperationPlan $dop): bool
    {
        $dop->loadMissing(['picBerauCoal', 'pengawasMitraKerja']);

        $shift = null;
        if ($dop->picBerauCoal->isNotEmpty()) {
            $shift = $dop->picBerauCoal->first()->shift;
        } elseif ($dop->pengawasMitraKerja->isNotEmpty()) {
            $shift = $dop->pengawasMitraKerja->first()->shift;
        }

        $pengawasLangsung = $dop->pengawasMitraKerja->isNotEmpty()
            ? $dop->pengawasMitraKerja->pluck('nama_pengawas')->implode(', ')
            : null;

        $perusahaanPic = null;
        if (! empty($dop->perusahaan)) {
            $namaPicBc = $dop->picBerauCoal->isNotEmpty()
                ? $dop->picBerauCoal->pluck('nama_pic')->map(fn ($n) => trim((string) $n))->filter()->values()->all()
                : [];
            $isSameAsPic = in_array(trim((string) $dop->perusahaan), $namaPicBc, true);
            if (! $isSameAsPic) {
                $perusahaanPic = $dop->perusahaan;
            }
        }

        $aktivitasText = ! empty($dop->aktivitas) ? $dop->aktivitas : $dop->pekerjaan;
        $tanggalStr = Carbon::parse($dop->tanggal)->toDateString();

        $result = RosterPlanning::updateOrCreate(
            [
                'source_type' => 'DOP',
                'source_id' => (string) $dop->id,
                'tanggal' => $tanggalStr,
            ],
            [
                'shift' => $shift,
                'site' => $dop->unit_id,
                'aktivitas' => $aktivitasText,
                'lokasi' => $dop->lokasi,
                'detail_lokasi' => $dop->detail_lokasi,
                'pengawas_langsung' => $pengawasLangsung,
                'perusahaan_pic' => $perusahaanPic,
                'kategori_area' => null,
                'no_ikk' => null,
                'id_detail_lokasi' => null,
                'jenis_sap' => null,
            ]
        );

        $wasNew = $result->wasRecentlyCreated;
        if ($wasNew) {
            $result->update(['status' => 'draft']);
        }

        return $wasNew;
    }
}
