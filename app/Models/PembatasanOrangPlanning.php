<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembatasanOrangPlanning extends Model
{
    protected $table = 'pembatasan_orang_planning';

    protected $fillable = [
        'tanggal_plan',
        'shift',
        'status',
        'sid',
        'nama',
        'nik',
        'nama_perusahaan',
        'site',
        'dept',
        'lokasi',
        'detail_lokasi',
        'creator_id',
        'creator_name',
        'control_room',
        'aktivitas',
        'catatan',
        'checked_in_at',
    ];

    protected $casts = [
        'tanggal_plan' => 'date',
        'shift' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
