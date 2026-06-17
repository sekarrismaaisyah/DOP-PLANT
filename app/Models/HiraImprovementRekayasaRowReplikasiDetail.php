<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HiraImprovementRekayasaRowReplikasiDetail extends Model
{
    protected $table = 'hira_improvement_rekayasa_row_replikasi_details';

    protected $fillable = [
        'rekayasa_row_id',
        'site',
        'perusahaan',
        'aktivitas',
        'kategori_rekayasa',
        'origin_replikasi',
        'pengendalian_rekayasa',
        'penjelasan_proses_kerja',
        'deteksi',
        'intervensi',
        'level_efektivitas',
        'nilai_risiko_awal',
        'prediksi_penurunan_risiko',
        'prediksi_risiko_sisa',
        'target',
        'total_populasi',
        'target_replikasi_komitmen',
        'aktual_replikasi',
        'satuan',
        'jumlah_mitra_replikasi',
        'tercover_behira',
        'potensi_peningkatan_level_efektivitas',
        'pengendalian_pen_tingkatan_level_efektivitas',
        'target_standar_isasi_due_date',
    ];

    /**
     * @return BelongsTo<HiraImprovementRekayasaRow, $this>
     */
    public function rekayasaRow(): BelongsTo
    {
        return $this->belongsTo(HiraImprovementRekayasaRow::class, 'rekayasa_row_id');
    }
}
