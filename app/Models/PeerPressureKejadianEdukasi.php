<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeerPressureKejadianEdukasi extends Model
{
    protected $table = 'peer_pressure_kejadian_edukasi';

    protected $fillable = [
        'tanggal_temuan',
        'jam_temuan',
        'kelompok_lokasi_temuan',
        'lokasi_temuan',
        'kelompok_lokasi_edukasi',
        'lokasi_edukasi',
        'tanggal_edukasi',
        'jam_edukasi',
        'perusahaan',
        'tasklist_temuan',
        'kronologi_temuan',
        'kategori_deviasi',
        'pemimpin_edukasi',
        'id_berecord',
        'jenis_kelompok_kerja',
        'kelompok_aktivitas_pekerjaan',
        'aktivitas_pekerjaan',
        'departemen',
        'evidence_url',
        'durasi_edukasi_menit',
        'status_pelaksanaan_edukasi',
    ];

    protected $casts = [
        'tanggal_temuan' => 'date',
        'tanggal_edukasi' => 'date',
    ];

    public function peserta(): HasMany
    {
        return $this->hasMany(PeerPressurePesertaEdukasi::class, 'kejadian_edukasi_id')->orderBy('urutan');
    }
}
