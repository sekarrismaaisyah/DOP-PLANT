<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DopmWeeklyImport extends Model
{
    use HasFactory;

    protected $table = 'dopm_weekly_imports';

    protected $fillable = [
        'row_no',
        'kode_ikk',
        'tanggal',
        'site',
        'jenis_ijin_kerja_khusus',
        'nama_pekerjaan',
        'perusahaan',
        'status_wp',
        'pic_approver',
        'nama_layer_1',
        'sid_layer_1',
        'nama_layer_2',
        'sid_layer_2',
        'nama_layer_3',
        'sid_layer_3',
        'nama_layer_4',
        'sid_layer_4',
        'start_date',
        'end_date',
        'location',
        'location_detail',
        'ada_ipk',
        'kode_ipk',
        'detail_ipk',
        'ada_okk',
        'kode_okk',
        'detail_okk',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'ada_ipk' => 'boolean',
        'ada_okk' => 'boolean',
    ];
}
