<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiNonKritis extends Model
{
    use HasFactory;

    protected $table = 'lokasi_non_kritis';

    protected $fillable = [
        'tanggal',
        'id_site',
        'site',
        'id_lokasi',
        'lokasi',
        'id_detil_lokasi',
        'detil_lokasi',
        'kategori_area',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const KATEGORI_KRITIS = 'kritis';
    public const KATEGORI_NON_KRITIS = 'non_kritis';
}
