<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedBanStatus: string
{
    case OpenBanned = 'open_banned';
    case OverdueBanned = 'overdue_banned';
    case OnTreatmentBanned = 'on_treatment_banned';
    case CloseBanned = 'close_banned';
    case OpenUnbanned = 'open_unbanned';
    case OverdueUnbanned = 'overdue_unbanned';
    case ClosedUnbanned = 'closed_unbanned';

    public function label(): string
    {
        return match ($this) {
            self::OpenBanned => 'Open Banned',
            self::OverdueBanned => 'Overdue Banned',
            self::OnTreatmentBanned => 'On Treatment Banned',
            self::CloseBanned => 'Close Banned',
            self::OpenUnbanned => 'Open Un Banned',
            self::OverdueUnbanned => 'Overdue Un Banned',
            self::ClosedUnbanned => 'Closed Un Banned',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OpenBanned, self::OpenUnbanned => 'ab-badge--wait',
            self::OverdueBanned, self::OverdueUnbanned => 'ab-badge--danger',
            self::OnTreatmentBanned => 'ab-badge--info',
            self::CloseBanned => 'ab-badge--muted',
            self::ClosedUnbanned => 'ab-badge--ok',
        };
    }
}
