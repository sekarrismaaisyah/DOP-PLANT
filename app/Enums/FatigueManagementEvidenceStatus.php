<?php

declare(strict_types=1);

namespace App\Enums;

enum FatigueManagementEvidenceStatus: string
{
    case BelumUpload = 'belum_upload';
    case SudahUpload = 'sudah_upload';
    case PerluLengkap = 'perlu_lengkap';
    case Terverifikasi = 'terverifikasi';

    public function label(): string
    {
        return match ($this) {
            self::BelumUpload => 'Belum Upload',
            self::SudahUpload => 'Sudah Upload',
            self::PerluLengkap => 'Perlu Dilengkapi',
            self::Terverifikasi => 'Terverifikasi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BelumUpload => 'gray',
            self::SudahUpload => 'blue',
            self::PerluLengkap => 'amber',
            self::Terverifikasi => 'green',
        };
    }
}
