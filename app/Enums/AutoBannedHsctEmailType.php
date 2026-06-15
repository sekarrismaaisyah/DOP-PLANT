<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedHsctEmailType: string
{
    case Initial = 'initial';
    case Reminder = 'reminder';

    public function label(): string
    {
        return match ($this) {
            self::Initial => 'Email Awal (Selasa)',
            self::Reminder => 'Email Reminder',
        };
    }
}
