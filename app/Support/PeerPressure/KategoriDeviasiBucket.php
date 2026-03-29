<?php

declare(strict_types=1);

namespace App\Support\PeerPressure;

/**
 * Normalisasi teks kategori_deviasi ke bucket statistik (sama dengan chart trend).
 */
final class KategoriDeviasiBucket
{
    public const LAINNYA = 'lainnya';

    /**
     * Lima kategori utama + lainnya (kunci selaras GetPeerPressureDashboardWeeklyTrendAction).
     */
    public static function bucket(mixed $raw): string
    {
        $k = mb_strtolower(trim((string) $raw));
        if ($k === '') {
            return self::LAINNYA;
        }

        if (str_contains($k, 'golden')) {
            return 'pelanggaran_golden_rules';
        }
        if (str_contains($k, 'pspp')) {
            return 'pelanggaran_pspp';
        }
        if (str_contains($k, 'blindspot')) {
            return 'blindspot_to_be_concerned';
        }
        if (str_contains($k, 'tidak speak')
            || (str_contains($k, 'speak up') && str_contains($k, 'fatigue'))
            || (str_contains($k, 'speak') && str_contains($k, 'fatigue') && str_contains($k, 'up'))) {
            return 'tidak_speak_up_fatigue';
        }
        if (str_contains($k, 'insiden')) {
            return 'insiden';
        }

        return self::LAINNYA;
    }

    /** @return list<string> */
    public static function trackedComplianceBuckets(): array
    {
        return [
            'tidak_speak_up_fatigue',
            'blindspot_to_be_concerned',
            'pelanggaran_pspp',
            'pelanggaran_golden_rules',
            'insiden',
        ];
    }

    public static function isNonBerecordPolicyBucket(string $bucket): bool
    {
        return $bucket === 'tidak_speak_up_fatigue' || $bucket === 'blindspot_to_be_concerned';
    }

    public static function isBerecordPolicyBucket(string $bucket): bool
    {
        return $bucket === 'pelanggaran_pspp'
            || $bucket === 'pelanggaran_golden_rules'
            || $bucket === 'insiden';
    }
}
