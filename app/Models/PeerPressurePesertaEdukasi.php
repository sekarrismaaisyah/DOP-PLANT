<?php

declare(strict_types=1);

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

    public function initials(): string
    {
        $nama = trim((string) $this->nama);
        if ($nama === '' || $nama === '-') {
            return '?';
        }

        $parts = preg_split('/\s+/u', $nama, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false || $parts === []) {
            return mb_strtoupper(mb_substr($nama, 0, min(2, mb_strlen($nama))));
        }

        if (count($parts) >= 2) {
            $first = $parts[0];
            $last = $parts[count($parts) - 1];

            return mb_strtoupper(mb_substr($first, 0, 1) . mb_substr($last, 0, 1));
        }

        return mb_strtoupper(mb_substr($nama, 0, min(2, mb_strlen($nama))));
    }
}
