<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntervensiKesiapanOrang extends Model
{
    use HasFactory;

    protected $table = 'intervensi_kesiapan_orang';

    protected $fillable = [
        'lokasi',
        'area_kerja',
        'nama_pja',
        'tipe_pja',
        'perusahaan',
        'id_employee',
        'nama_karyawan',
        'pic_id',
        'pic_username',
        'pic_nama',
        'pic_telepon',
        'issue',
        'resolution',
        'evidence_path',
        'status',
        'closed_at',
        'closed_by',
        'created_by',
        'created_by_email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'closed_at' => 'datetime',
    ];
}

