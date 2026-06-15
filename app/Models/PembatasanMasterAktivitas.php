<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatasanMasterAktivitas extends Model
{
    protected $table = 'pembatasan_master_aktivitas';

    protected $fillable = [
        'site',
        'perusahaan',
        'departemen',
        'kategori_aktivitas_luar_kabin',
        'detail_aktivitas_pengoperasian_lv',
        'frekuensi_aktivitas_per_shift',
        'estimasi_jumlah_lv_per_shift',
    ];

    protected $casts = [
        'frekuensi_aktivitas_per_shift' => 'integer',
        'estimasi_jumlah_lv_per_shift' => 'integer',
    ];
}
