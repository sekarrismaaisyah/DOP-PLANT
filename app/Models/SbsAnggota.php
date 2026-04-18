<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SbsAnggota extends Model
{
    protected $table = 'sbs_anggota';

    protected $fillable = [
        'kelompok_id',
        'nama',
        'sid',
        'urutan',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(SbsKelompok::class, 'kelompok_id');
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
