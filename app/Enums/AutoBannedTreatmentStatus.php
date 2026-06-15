<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedTreatmentStatus: string
{
    case None = 'none';
    case NeedSubmit = 'need_submit';
    case Overdue = 'overdue';
    case Submitted = 'submitted';

    public function label(): string
    {
        return match ($this) {
            self::None => '—',
            self::NeedSubmit => 'Need Submit Treatment',
            self::Overdue => 'Treatment Overdue',
            self::Submitted => 'Treatment Submitted',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::None => 'ab-badge--muted',
            self::NeedSubmit => 'ab-badge--wait',
            self::Overdue => 'ab-badge--danger',
            self::Submitted => 'ab-badge--ok',
        };
    }
}
