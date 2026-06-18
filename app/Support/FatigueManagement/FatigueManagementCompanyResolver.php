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
        'HRB' => 'PT Hutan Rindang Banua',
        'SCI' => 'PT SUCOFINDO',
        'SCCI' => 'PT. Surveyor Carbon Consulting Indonesia',
        'ACI' => 'PT Arcistec International',
        'BAR' => 'PT Bumi Artlantis Raya',
        'OPP' => 'PT ORECON PUTRA PERKASA',
        'BUMA' => 'PT Bukit Makmur Mandiri Utama',
        'PAMA' => 'PT Pamapersada Nusantara',
        'KDC' => 'PT Kaltim Diamond Coal',
        'MOP' => 'Mulia Oto Partindo',
        'DNX' => 'DNX',
        'DAN' => 'DAN',
        'TMU' => 'Tectona Mitra Utama',
        'MTL' => 'PT Mutiara Tanjung Lestari',
        'MTN' => 'PT Madhani Talatah Nusantara',
        'FAD' => 'PT Fajar Anugerah Dinamika',
    ];

    /** @var array<string, string> */
    private const COMPANY_ALIASES = [
        'PT HRB' => 'HRB',
        'PT HUTAN RINDANG BANUA' => 'HRB',
        'PT SUCOFINDO' => 'SCI',
        'PT. SUCOFINDO' => 'SCI',
        'PT SUCOFINDO / SCI' => 'SCI',
        'PT. SURVEYOR CARBON CONSULTING INDONESIA' => 'SCCI',
        'PT SURVEYOR CARBON CONSULTING INDONESIA' => 'SCCI',
        'PT ARCISTEC INTERNATIONAL' => 'ACI',
        'PT BUMI ARTLANTIS RAYA' => 'BAR',
        'PT ORECON PUTRA PERKASA' => 'OPP',
        'PAMA GMO' => 'PAMA',
        'PAMA' => 'PAMA',
        'PT PAMAPERSADA NUSANTARA' => 'PAMA',
        'KDC GMO' => 'KDC',
        'KDC' => 'KDC',
        'MULIA OTO PARTINDO' => 'MOP',
        'MOP GMO' => 'MOP',
        'DNX GMO' => 'DNX',
        'DAN GMO' => 'DAN',
        'TECTONA MITRA UTAMA' => 'TMU',
        'TMU GMO' => 'TMU',
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

        if (isset(self::COMPANY_ALIASES[$normalized])) {
            return self::COMPANY_ALIASES[$normalized];
        }

        foreach (self::PARTNER_TO_COMPANY as $partner => $label) {
            if (self::normalizeKey($label) === $normalized) {
                return $partner;
            }
        }

        foreach (self::PARTNER_TO_COMPANY as $partner => $label) {
            if (str_contains($normalized, self::normalizeKey($partner))
                || str_contains($normalized, self::normalizeKey($label))) {
                return $partner;
            }
        }

        return trim((string) $company);
    }

    /**
     * Resolve partner key dari email user (contoh: pama@gmail.com → PAMA).
     */
    public static function partnerKeyFromEmail(?string $email): ?string
    {
        $email = trim((string) $email);
        if ($email === '' || ! str_contains($email, '@')) {
            return null;
        }

        $local = mb_strtolower(trim((string) strtok($email, '@')));
        if ($local === '') {
            return null;
        }

        $aliases = config('fatigue_management.email_partner_aliases', []);
        foreach ($aliases as $alias => $partnerKey) {
            $alias = mb_strtolower((string) $alias);
            if ($local === $alias || self::localPartMatchesAlias($local, $alias)) {
                return self::isKnownPartnerKey((string) $partnerKey)
                    ? strtoupper((string) $partnerKey)
                    : null;
            }
        }

        if (isset($aliases[$local]) && self::isKnownPartnerKey((string) $aliases[$local])) {
            return strtoupper((string) $aliases[$local]);
        }

        foreach (array_keys(self::PARTNER_TO_COMPANY) as $partnerKey) {
            $keyLower = mb_strtolower($partnerKey);
            if ($local === $keyLower) {
                return $partnerKey;
            }
            if (str_starts_with($local, $keyLower . '.') || str_starts_with($local, $keyLower . '_') || str_starts_with($local, $keyLower . '-')) {
                return $partnerKey;
            }
        }

        $resolved = self::companyToPartner($local);

        return self::isKnownPartnerKey($resolved) ? strtoupper($resolved) : null;
    }

    /**
     * Resolve partner key dari nama user (contoh: "PAMA" atau "PAMA HSE" → PAMA).
     */
    public static function partnerKeyFromName(?string $name): ?string
    {
        $normalized = mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $name)));
        if ($normalized === '') {
            return null;
        }

        $aliases = config('fatigue_management.email_partner_aliases', []);
        foreach ($aliases as $alias => $partnerKey) {
            $alias = mb_strtolower((string) $alias);
            if ($normalized === $alias || str_starts_with($normalized, $alias . ' ')
                || str_starts_with($normalized, $alias . '.')
                || str_starts_with($normalized, $alias . '_')
                || str_starts_with($normalized, $alias . '-')) {
                return self::isKnownPartnerKey((string) $partnerKey)
                    ? strtoupper((string) $partnerKey)
                    : null;
            }
        }

        foreach (array_keys(self::PARTNER_TO_COMPANY) as $partnerKey) {
            $keyLower = mb_strtolower($partnerKey);
            if ($normalized === $keyLower || str_starts_with($normalized, $keyLower . ' ')) {
                return $partnerKey;
            }
        }

        $resolved = self::companyToPartner($normalized);

        return self::isKnownPartnerKey($resolved) ? strtoupper($resolved) : null;
    }

    /**
     * Resolve partner dari email lalu nama user.
     */
    public static function partnerKeyFromUser(?string $email, ?string $name): ?string
    {
        return self::partnerKeyFromEmail($email) ?? self::partnerKeyFromName($name);
    }

    private static function localPartMatchesAlias(string $local, string $alias): bool
    {
        return str_starts_with($local, $alias . '.')
            || str_starts_with($local, $alias . '_')
            || str_starts_with($local, $alias . '-');
    }

    public static function isKnownPartnerKey(?string $partnerKey): bool
    {
        $key = self::normalizeKey($partnerKey);

        return $key !== '' && isset(self::PARTNER_TO_COMPANY[$key]);
    }

    /**
     * @return list<string>
     */
    public static function allCompanyLabels(): array
    {
        return array_values(self::PARTNER_TO_COMPANY);
    }
}
