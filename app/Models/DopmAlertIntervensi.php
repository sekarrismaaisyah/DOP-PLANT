<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DopmAlertIntervensi extends Model
{
    protected $table = 'dopm_alert_intervensi';

    protected $fillable = [
        'tanggal',
        'kode_ikk',
        'alert_level',
        'user_id',
        'user_name',
        'user_username',
        'user_email',
        'pic_user_id',
        'pic_name',
        'pic_email',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'alert_level' => 'integer',
        'user_id' => 'integer',
        'pic_user_id' => 'integer',
    ];

    /**
     * Relasi ke user PIC (Person In Charge).
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    /**
     * Relasi ke closure (data penutupan issue).
     */
    public function closure(): HasOne
    {
        return $this->hasOne(DopmAlertIntervensiClosure::class, 'alert_intervensi_id');
    }

    /**
     * Cek apakah issue sudah di-close.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Cek apakah issue masih open.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Cek apakah issue sedang in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

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

    /**
     * Ambil daftar level alert (1, 2, 3) yang sudah terintervensi per IKK untuk tanggal.
     * Return: ['kode_ikk' => [1, 2], ...]
     */
    public static function getIntervensiLevelsByIkk(string $tanggal): array
    {
        return self::query()
            ->where('tanggal', $tanggal)
            ->get()
            ->groupBy('kode_ikk')
            ->map(fn ($rows) => $rows->pluck('alert_level')->unique()->values()->all())
            ->all();
    }

    /**
     * Ambil detail intervensi per IKK per level (termasuk user_name) untuk tanggal.
     * Return: ['kode_ikk' => [alert_level => ['user_name' => '...', 'user_username' => '...'], ...], ...]
     */
    public static function getIntervensiDetailByIkk(string $tanggal): array
    {
        $rows = self::query()
            ->where('tanggal', $tanggal)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $kode = $row->kode_ikk ?? '';
            $level = (int) $row->alert_level;
            if ($kode === '') {
                continue;
            }
            if (! isset($result[$kode])) {
                $result[$kode] = [];
            }
            $result[$kode][$level] = [
                'user_name' => $row->user_name ?? null,
                'user_username' => $row->user_username ?? null,
            ];
        }

        return $result;
    }
}
