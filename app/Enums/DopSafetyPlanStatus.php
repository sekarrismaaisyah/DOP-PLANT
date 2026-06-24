<?php

declare(strict_types=1);

namespace App\Enums;

enum DopSafetyPlanStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Revision = 'revision';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingApproval => 'Menunggu Approval',
            self::Approved => 'Final — Approved',
            self::Revision => 'Revisi — SH',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'ds-badge',
            self::PendingApproval => 'ds-badge ds-badge--warning',
            self::Approved => 'ds-badge ds-badge--success',
            self::Revision => 'ds-badge ds-badge--danger',
        };
    }
}
