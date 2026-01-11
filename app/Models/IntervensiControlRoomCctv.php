<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntervensiControlRoomCctv extends Model
{
    use HasFactory;

    protected $table = 'intervensi_control_room_cctv';

    protected $fillable = [
        'intervensi_id',
        'cctv_id',
        'status_done',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

