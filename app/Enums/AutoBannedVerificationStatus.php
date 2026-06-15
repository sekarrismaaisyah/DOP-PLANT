<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedVerificationStatus: string
{
    case None = 'none';
    case NeedVerifikasi = 'need_verifikasi';
    case Overdue = 'overdue';
    case Done = 'done';
    case DoneOverdue = 'done_overdue';

    public function label(): string
    {
        return match ($this) {
            self::None => '—',
            self::NeedVerifikasi => 'Need Verifikasi SOD',
            self::Overdue => 'Verifikasi SOD Overdue',
            self::Done => 'Done',
            self::DoneOverdue => 'Done Overdue',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::None => 'ab-badge--muted',
            self::NeedVerifikasi => 'ab-badge--wait',
            self::Overdue => 'ab-badge--danger',
            self::Done, self::DoneOverdue => 'ab-badge--ok',
        };
    }
}
