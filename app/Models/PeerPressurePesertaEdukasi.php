<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerPressurePesertaEdukasi extends Model
{
    protected $table = 'peer_pressure_peserta_edukasi';

    protected $fillable = [
        'kejadian_edukasi_id',
        'sid',
        'nama',
        'peran',
        'urutan',
    ];

    public function kejadian(): BelongsTo
    {
        return $this->belongsTo(PeerPressureKejadianEdukasi::class, 'kejadian_edukasi_id');
    }
}
