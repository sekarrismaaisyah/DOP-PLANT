<?php

declare(strict_types=1);

namespace App\Support\FatigueManagement;

/**
 * Pemetaan singkatan mitra kerja ke label perusahaan resmi.
 */
final class FatigueManagementCompanyResolver
{
    /** @var array<string, string> */
    public const PARTNER_TO_COMPANY = [
        'BUMA' => 'PT Bukit Makmur Mandiri Utama',
        'PAMA' => 'PT Pamapersada Nusantara',
        'KDC' => 'PT Kaltim Diamond Coal',
        'MTL' => 'PT Mutiara Tanjung Lestari',
        'MTN' => 'PT Madhani Talatah Nusantara',
        'BAR' => 'PT Bumi Artlantis Raya',
        'FAD' => 'PT Fajar Anugerah Dinamika',
    ];

    public static function normalizeKey(?string $value): string
    {
        $s = preg_replace('/\s+/u', ' ', trim((string) $value));

        return mb_strtoupper($s);
    }

    public static function partnerToCompany(?string $partner): string
    {
        $key = self::normalizeKey($partner);

        return self::PARTNER_TO_COMPANY[$key] ?? trim((string) $partner);
    }

    public static function companyToPartner(?string $company): string
    {
        $normalized = self::normalizeKey($company);
        foreach (self::PARTNER_TO_COMPANY as $partner => $label) {
            if (self::normalizeKey($label) === $normalized) {
                return $partner;
            }
        }

        return trim((string) $company);
    }

    /**
     * @return list<string>
     */
    public static function allCompanyLabels(): array
    {
        return array_values(self::PARTNER_TO_COMPANY);
    }
}
