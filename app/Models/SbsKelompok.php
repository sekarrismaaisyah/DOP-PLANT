<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SbsKelompok extends Model
{
    protected $table = 'sbs_kelompok';

    protected $fillable = [
        'site',
        'perusahaan',
        'level_grup',
        'nama_kelompok',
        'nama_bapak_asuh',
        'sid_bapak_asuh',
    ];

    public function anggota(): HasMany
    {
        return $this->hasMany(SbsAnggota::class, 'kelompok_id')->orderBy('urutan');
    }

    public function bapakAsuhInitials(): string
    {
        $nama = trim((string) $this->nama_bapak_asuh);
        if ($nama === '') {
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
