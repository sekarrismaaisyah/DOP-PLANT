<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DopmWaNotificationLog extends Model
{
    protected $table = 'dopm_wa_notification_log';

    protected $fillable = [
        'tanggal',
        'kode_ikk',
        'alert_level',
        'layer',
        'phone_number',
        'recipient_name',
        'recipient_sid',
        'message',
        'fonnte_status',
        'fonnte_id',
        'fonnte_response',
        'sent_at',
        'provider',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'alert_level' => 'integer',
        'layer' => 'integer',
        'sent_at' => 'datetime',
    ];

    /**
     * Cek apakah notifikasi sudah pernah dikirim untuk kombinasi tertentu.
     */
    public static function alreadySent(
        string $tanggal,
        string $kodeIkk,
        int $alertLevel,
        int $layer,
        string $phoneNumber
    ): bool {
        return self::where('tanggal', $tanggal)
            ->where('kode_ikk', $kodeIkk)
            ->where('alert_level', $alertLevel)
            ->where('layer', $layer)
            ->where('phone_number', $phoneNumber)
            ->where('fonnte_status', 'success')
            ->exists();
    }

    /**
     * Get semua notifikasi yang sudah dikirim untuk tanggal dan kode IKK tertentu.
     */
    public static function getSentNotifications(string $tanggal, string $kodeIkk): array
    {
        return self::where('tanggal', $tanggal)
            ->where('kode_ikk', $kodeIkk)
            ->where('fonnte_status', 'success')
            ->get()
            ->groupBy(fn ($row) => $row->alert_level . '_' . $row->layer . '_' . $row->phone_number)
            ->keys()
            ->toArray();
    }
}
