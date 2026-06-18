<?php

declare(strict_types=1);

namespace App\Enums;

enum FatigueManagementEvaluationStatus: string
{
    case MenungguEvidence = 'menunggu_evidence';
    case MenungguReview = 'menunggu_review';
    case DalamEvaluasi = 'dalam_evaluasi';
    case PerluPerbaikan = 'perlu_perbaikan';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::MenungguEvidence => 'Menunggu Evidence',
            self::MenungguReview => 'Menunggu Review',
            self::DalamEvaluasi => 'Dalam Evaluasi',
            self::PerluPerbaikan => 'Perlu Perbaikan',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MenungguEvidence => 'gray',
            self::MenungguReview => 'blue',
            self::DalamEvaluasi => 'indigo',
            self::PerluPerbaikan => 'amber',
            self::Disetujui => 'green',
            self::Ditolak => 'red',
        };
    }
}
