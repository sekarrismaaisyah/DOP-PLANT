<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dopm extends Model
{
    use HasFactory;

    protected $table = 'dopm';

    protected $fillable = [
        'id_dop',
        'timestamp',
        'site_ijin_kerja_khusus',
        'perusahaan_ijin_kerja_khusus',
        'jenis_ijin_kerja_khusus',
        'kode_ikk',
        'tanggal_selesai_ijin',
        'nama_pekerjaan',
        'tanggal_dop',
        'status_pengiriman_notif',
        'status',
        'deskripsi_atau_alasan_cancel',
        'sid_layer_1',
        'nama_layer_1',
        'shift',
        'jam_mulai',
        'jam_akhir',
        'sid_layer_2',
        'nama_layer_2',
        'sid_layer_3',
        'nama_layer_3',
        'sid_layer_4',
        'nama_layer_4',
        'jenis_pengawasan_layer',
        'detail_lokasi',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'tanggal_selesai_ijin' => 'date',
        'tanggal_dop' => 'date',
    ];
}
