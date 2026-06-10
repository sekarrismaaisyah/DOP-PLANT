<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatasanBatasLvPerLokasi extends Model
{
    protected $table = 'pembatasan_batas_lv_per_lokasi';

    protected $fillable = [
        'site',
        'lokasi',
        'detail_lokasi',
        'batas_lv',
    ];

    protected $casts = [
        'batas_lv' => 'integer',
    ];
}
