<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntervensiControlRoom extends Model
{
    use HasFactory;

    protected $table = 'intervensi_control_room';

    protected $fillable = [
        'control_room',
        'pic_id',
        'pic_username',
        'pic_nama',
        'pic_telepon',
        'issue',
        'status',
        'closed_at',
        'closed_by',
        'created_by',
        'created_by_email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

