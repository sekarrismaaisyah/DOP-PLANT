<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HiraImprovementRekayasaRow extends Model
{
    protected $table = 'hira_improvement_rekayasa_rows';

    protected $fillable = [
        'company',
        'period_year',
        'aktivitas',
        'site_perusahaan',
        'pengendalian_rekayasa',
        'deteksi',
        'intervensi',
        'prediksi_penurunan_risiko',
        'penjelasan_proses_kerja',
        'sort_order',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * @return HasOne<HiraImprovementRekayasaRowReplikasiDetail, $this>
     */
    public function replikasiDetail(): HasOne
    {
        return $this->hasOne(HiraImprovementRekayasaRowReplikasiDetail::class, 'rekayasa_row_id');
    }
}
