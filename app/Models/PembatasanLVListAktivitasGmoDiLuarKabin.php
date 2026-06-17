<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatasanLVListAktivitasGmoDiLuarKabin extends Model
{
    public $timestamps = false;

    protected $table = 'list_aktivitas_gmo_di_luar_kabin';

    protected $fillable = [
        'site',
        'perusahaan',
        'kategori_aktivitas_luar_kabin',
        'detail_aktivitas_luar_kabin',
        'frekuensi_aktivitas',
        'potensi_risiko',
        'kategori_kontrol_existing',
        'aktual_kontrol_existing',
        'kategori_potensi_kontrol_essr',
        'potensi_kontrol_essr',
        'rencana_pemenuhan',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
