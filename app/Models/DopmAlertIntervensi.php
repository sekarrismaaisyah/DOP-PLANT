<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DopmAlertIntervensi extends Model
{
    protected $table = 'dopm_alert_intervensi';

    protected $fillable = [
        'tanggal',
        'kode_ikk',
        'alert_level',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'alert_level' => 'integer',
    ];

    /**
     * Cek apakah IKK sudah terintervensi di level tertentu pada tanggal.
     * Digunakan untuk menyembunyikan alert level 3 jika sudah intervensi di level 2.
     */
    public static function hasIntervensiAtLevel(string $tanggal, string $kodeIkk, int $alertLevel): bool
    {
        return self::query()
            ->where('tanggal', $tanggal)
            ->where('kode_ikk', $kodeIkk)
            ->where('alert_level', $alertLevel)
            ->exists();
    }

    /**
     * Ambil level intervensi tertinggi per IKK untuk tanggal (1, 2, atau 3).
     * Jika ada intervensi di level 2, maka alert level 3 tidak perlu ditampilkan.
     */
    public static function getMaxIntervensiLevelByIkk(string $tanggal): array
    {
        return self::query()
            ->where('tanggal', $tanggal)
            ->get()
            ->groupBy('kode_ikk')
            ->map(fn ($rows) => $rows->max('alert_level'))
            ->all();
    }
}
