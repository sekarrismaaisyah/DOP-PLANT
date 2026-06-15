<?php

declare(strict_types=1);

namespace App\Enums;

enum AutoBannedStatusChangeType: string
{
    case Initial = 'initial';
    case PassToNotPass = 'pass_to_not_pass';
    case NotPassToPass = 'not_pass_to_pass';
    case StatusUpdate = 'status_update';

    public function label(): string
    {
        return match ($this) {
            self::Initial => 'Pertama Terdeteksi',
            self::PassToNotPass => 'Pass → Not Passed',
            self::NotPassToPass => 'Not Passed → Passed',
            self::StatusUpdate => 'Update Status',
        };
    }
}
