<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedHsctSyncStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
    case NotRequired = 'not_required';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Belum Dikirim',
            self::Sent => 'Terkirim HSECT',
            self::Confirmed => 'Dikonfirmasi HSECT',
            self::Failed => 'Gagal Kirim',
            self::NotRequired => 'Tidak Perlu',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'ab-badge--wait',
            self::Sent => 'ab-badge--info',
            self::Confirmed => 'ab-badge--ok',
            self::Failed => 'ab-badge--danger',
            self::NotRequired => 'ab-badge--muted',
        };
    }
}
