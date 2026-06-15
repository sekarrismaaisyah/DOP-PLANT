<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedHsctCampaignStatus: string
{
    case Active = 'active';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Completed => 'Selesai (Semua Banned)',
        };
    }
}
