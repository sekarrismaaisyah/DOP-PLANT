<?php

declare(strict_types=1);

namespace App\Support\PeerPressure;

/**
 * Kontraktor yang diikutkan dalam KPI pelaksanaan baseline & ringkasan per site pada dashboard-peer.
 */
final class PeerPressureDashboardRestrictedContractors
{
    /** @var list<string> */
    public const COMPANIES = [
        'PT Bukit Makmur Mandiri Utama',
        'PT Kaltim Diamond Coal',
        'PT Mutiara Tanjung Lestari',
        'PT Pamapersada Nusantara',
        'PT Bumi Artlantis Raya',
        'PT Fajar Anugerah Dinamika',
        'PT Madhani Talatah Nusantara',
    ];

    /** @var array<string, true>|null */
    private static ?array $normalizedSet = null;

    public static function normalizeLabel(?string $name): string
    {
        $s = preg_replace('/\s+/u', ' ', trim((string) $name));

        return mb_strtolower($s);
    }

    public static function isAllowedCompanyLabel(?string $label): bool
    {
        $k = self::normalizeLabel($label);
        if ($k === '') {
            return false;
        }
        if (self::$normalizedSet === null) {
            self::$normalizedSet = [];
            foreach (self::COMPANIES as $c) {
                self::$normalizedSet[self::normalizeLabel($c)] = true;
            }
        }

        return isset(self::$normalizedSet[$k]);
    }
}
