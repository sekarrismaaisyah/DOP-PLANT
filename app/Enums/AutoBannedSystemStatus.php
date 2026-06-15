<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedSystemStatus: string
{
    case Passed = 'passed';
    case NotPassed = 'not_passed';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'Passed',
            self::NotPassed => 'Not Passed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Passed => 'ab-badge--ok',
            self::NotPassed => 'ab-badge--danger',
        };
    }
}
