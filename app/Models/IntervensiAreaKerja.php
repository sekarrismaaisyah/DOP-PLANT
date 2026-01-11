<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntervensiAreaKerja extends Model
{
    use HasFactory;

    protected $table = 'intervensi_area_kerja';

    protected $fillable = [
        'lokasi',
        'area_kerja',
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

