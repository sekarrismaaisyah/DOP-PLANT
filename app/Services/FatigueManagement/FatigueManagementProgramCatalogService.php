<?php

declare(strict_types=1);

namespace App\Services\FatigueManagement;

use App\Support\FatigueManagement\FatigueManagementCompanyResolver;
use Illuminate\Support\Str;

/**
 * Katalog program terpadu: standar HO (M/U/R) + program mitra PD 2026.
 */
final class FatigueManagementProgramCatalogService
{
    /** @var array<string, int> */
    private const TYPE_SORT_ORDER = [
        'mandatory' => 0,
        'upgrade' => 1,
        'mitra' => 2,
    ];

    /**
     * @return array{document: array<string, mixed>, partners: list<array<string, mixed>>, programs: list<array<string, mixed>>}
     */
    public function buildCatalog(): array
    {
        $framework = $this->loadGmoFramework();
        $perkuatan = $this->loadPerkuatanData();

        $partners = $this->buildPartners($framework, $perkuatan);
        $partnerKeys = array_map(static fn (array $p): string => (string) ($p['key'] ?? ''), $partners);

        $programs = [];
        $programs = array_merge($programs, $this->hoStandardPrograms($framework, $partnerKeys));
        $programs = array_merge($programs, $this->mitraPrograms($perkuatan, $partnerKeys));

        usort($programs, static function (array $a, array $b): int {
            $ta = self::TYPE_SORT_ORDER[$a['program_type'] ?? 'mitra'] ?? 9;
            $tb = self::TYPE_SORT_ORDER[$b['program_type'] ?? 'mitra'] ?? 9;
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }

            $cmp = ($a['no'] ?? 0) <=> ($b['no'] ?? 0);
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
        });

        return [
            'document' => array_merge(
                $framework['document'] ?? [],
                [
                    'ho_classification' => $framework['ho_classification'] ?? [],
                    'perkuatan_title' => $perkuatan['metadata']['title'] ?? '',
                    'program_period' => $perkuatan['metadata']['default_program_period'] ?? [],
                ],
            ),
            'partners' => $partners,
            'programs' => $programs,
        ];
    }

    /**
     * @param  list<string>  $partnerKeys
     * @return list<array<string, mixed>>
     */
    private function hoStandardPrograms(array $framework, array $partnerKeys): array
    {
        $programs = [];

        foreach ($framework['site_standards'] ?? [] as $std) {
            $status = (string) ($std['status'] ?? '');
            if (! in_array($status, ['wajib', 'upgrade'], true)) {
                continue;
            }

            $no = (int) ($std['no'] ?? 0);
            $typeMeta = $this->hoTypeMeta($status);
            $targets = $this->resolveStandardTargets($std, $partnerKeys);

            if ($targets === []) {
                continue;
            }

            $key = $this->hoProgramKey($status, $no);
            $base = [
                'key' => $key,
                'no' => $no,
                'title' => (string) ($std['standard'] ?? ''),
                'program_type' => $typeMeta['program_type'],
                'program_type_label' => $typeMeta['label'],
                'ho_badge' => $typeMeta['badge'],
                'status' => $status,
                'frequency' => $this->frequencyForStandard($no),
                'frequency_raw' => $this->frequencyForStandard($no),
                'pillar' => $this->pillarForStandard($no),
                'partner_keys' => $targets,
                'source' => 'FMP-STD-001',
            ];

            foreach ($targets as $partnerKey) {
                $programs[] = array_merge($base, [
                    'partner_key' => $partnerKey,
                    'row_key' => $key . '|' . $partnerKey,
                ]);
            }
        }

        return $programs;
    }

    /**
     * @return array{program_type: string, label: string, badge: string}
     */
    private function hoTypeMeta(string $status): array
    {
        return match ($status) {
            'wajib' => [
                'program_type' => 'mandatory',
                'label' => 'Mandatory (M)',
                'badge' => 'M',
            ],
            'upgrade' => [
                'program_type' => 'upgrade',
                'label' => 'Upgrade (U)',
                'badge' => 'U',
            ],
            default => [
                'program_type' => 'mitra',
                'label' => 'Program Mitra',
                'badge' => '',
            ],
        };
    }

    private function hoProgramKey(string $status, int $no): string
    {
        $pad = str_pad((string) $no, 2, '0', STR_PAD_LEFT);

        return match ($status) {
            'wajib' => 'wajib-site-' . $pad,
            'upgrade' => 'ho-u-' . $pad,
            default => 'ho-std-' . $pad,
        };
    }

    /**
     * @param  array<string, mixed>  $std
     * @param  list<string>  $partnerKeys
     * @return list<string>
     */
    private function resolveStandardTargets(array $std, array $partnerKeys): array
    {
        $rules = $std['partner_targets'] ?? $std['mitra'] ?? null;
        if ($rules === null) {
            return $partnerKeys;
        }

        if (! is_array($rules)) {
            return $partnerKeys;
        }

        return $this->resolvePartnerTargets($rules, $partnerKeys);
    }

    /**
     * @param  list<string>  $partnerKeys
     * @return list<array<string, mixed>>
     */
    private function mitraPrograms(array $perkuatan, array $partnerKeys): array
    {
        $programs = [];

        foreach ($perkuatan['companies'] ?? [] as $company) {
            $companyName = (string) ($company['company'] ?? '');
            $partnerKey = FatigueManagementCompanyResolver::companyToPartner($companyName);

            if (! in_array($partnerKey, $partnerKeys, true)) {
                $partnerKeys[] = $partnerKey;
            }

            foreach ($company['programs'] ?? [] as $prog) {
                $programs = array_merge($programs, $this->flattenMitraProgram($prog, $companyName, $partnerKey));
            }
        }

        return $programs;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function flattenMitraProgram(array $prog, string $companyName, string $partnerKey): array
    {
        $no = (int) ($prog['no'] ?? 0);
        $title = trim((string) ($prog['program'] ?? ''));
        $slug = Str::slug(mb_substr($title, 0, 40));
        $key = 'mitra-' . Str::slug($partnerKey) . '-' . $no . '-' . $slug;
        $key = Str::limit($key, 64, '');

        $frequencyRaw = trim(str_replace("\n", ' ', (string) ($prog['frequency'] ?? 'Weekly')));
        $frequency = $this->normalizeFrequency($frequencyRaw);

        $base = [
            'key' => $key,
            'no' => $no,
            'title' => $title,
            'program_type' => 'mitra',
            'program_type_label' => 'Program Mitra',
            'ho_badge' => '',
            'status' => 'tentatif',
            'frequency' => $frequency,
            'frequency_raw' => $frequencyRaw,
            'pillar' => (string) ($prog['implementer'] ?? 'Mitra'),
            'partner_key' => $partnerKey,
            'partner_keys' => [$partnerKey],
            'company' => $companyName,
            'sites' => $prog['sites'] ?? [],
            'timeline' => $prog['timeline'] ?? [],
            'pic' => $prog['pic'] ?? '',
            'implementation_indicator' => $prog['implementation_indicator'] ?? '',
            'source' => $prog['source_sheet'] ?? 'Perkuatan PD 2026',
            'row_key' => $key . '|' . $partnerKey,
        ];

        $rows = [$base];

        foreach ($prog['subactivities'] ?? [] as $i => $sub) {
            $subTitle = trim((string) ($sub['description'] ?? ''));
            if ($subTitle === '') {
                continue;
            }

            $subKey = $key . '-sub' . ($i + 1);
            $subFreqRaw = trim(str_replace("\n", ' ', (string) ($sub['frequency'] ?? $frequencyRaw)));
            $rows[] = [
                'key' => $subKey,
                'no' => $no,
                'title' => $subTitle,
                'program_type' => 'mitra',
                'program_type_label' => 'Program Mitra (Sub)',
                'ho_badge' => '',
                'status' => 'tentatif',
                'frequency' => $this->normalizeFrequency($subFreqRaw),
                'frequency_raw' => $subFreqRaw,
                'pillar' => (string) ($sub['implementer'] ?? $prog['implementer'] ?? 'Mitra'),
                'partner_key' => $partnerKey,
                'partner_keys' => [$partnerKey],
                'company' => $companyName,
                'parent_program' => $title,
                'implementation_indicator' => $sub['implementation_indicator'] ?? ($prog['implementation_indicator'] ?? ''),
                'source' => $prog['source_sheet'] ?? 'Perkuatan PD 2026',
                'row_key' => $subKey . '|' . $partnerKey,
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $framework
     * @param  array<string, mixed>  $perkuatan
     * @return list<array<string, mixed>>
     */
    private function buildPartners(array $framework, array $perkuatan): array
    {
        $byKey = [];

        foreach ($framework['partners'] ?? [] as $partner) {
            $key = (string) ($partner['key'] ?? '');
            if ($key !== '') {
                $byKey[$key] = $partner;
            }
        }

        foreach ($perkuatan['companies'] ?? [] as $company) {
            $companyName = (string) ($company['company'] ?? '');
            $key = FatigueManagementCompanyResolver::companyToPartner($companyName);
            if (! isset($byKey[$key])) {
                $byKey[$key] = [
                    'key' => $key,
                    'name' => $companyName,
                    'classification' => 'medium',
                    'classification_label' => 'Mitra PD 2026',
                ];
            }
        }

        uasort($byKey, static fn (array $a, array $b): int => strcmp((string) ($a['key'] ?? ''), (string) ($b['key'] ?? '')));

        return array_values($byKey);
    }

    /**
     * @param  list<mixed>  $mitraRules
     * @param  list<string>  $allPartnerKeys
     * @return list<string>
     */
    private function resolvePartnerTargets(array $mitraRules, array $allPartnerKeys): array
    {
        if ($mitraRules === [] || in_array('ALL', $mitraRules, true)) {
            return $allPartnerKeys;
        }

        if (in_array('ALL_EXCEPT_MOP', $mitraRules, true)) {
            return array_values(array_filter($allPartnerKeys, static fn (string $k): bool => $k !== 'MOP'));
        }

        if (in_array('REPLICATE_PAMA', $mitraRules, true)) {
            return $allPartnerKeys;
        }

        $resolved = [];
        foreach ($mitraRules as $rule) {
            $key = strtoupper((string) $rule);
            if (in_array($key, $allPartnerKeys, true)) {
                $resolved[] = $key;
            }
        }

        return $resolved !== [] ? $resolved : $allPartnerKeys;
    }

    private function normalizeFrequency(string $raw): string
    {
        $lower = mb_strtolower($raw);

        if (str_contains($lower, 'shift')) {
            return 'Shiftly';
        }
        if (str_contains($lower, 'daily') || str_contains($lower, 'harian')) {
            return 'Daily';
        }
        if (str_contains($lower, 'weekly') || str_contains($lower, 'minggu')) {
            return 'Weekly';
        }
        if (str_contains($lower, 'awal shift')) {
            return 'Awal Shift';
        }

        return $raw !== '' ? $raw : 'Weekly';
    }

    private function frequencyForStandard(int $no): string
    {
        return match (true) {
            $no === 10 => 'Shiftly',
            in_array($no, [5, 6, 14, 15, 17, 19], true) => 'Daily',
            default => 'Weekly',
        };
    }

    private function pillarForStandard(int $no): string
    {
        return match (true) {
            $no <= 4 => 'Prevention & Awareness',
            $no <= 9, $no === 18 => 'Pre-Shift Control',
            $no <= 13, $no === 23 => 'In-Shift Monitoring & Recovery',
            default => 'Assurance & Governance',
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function loadGmoFramework(): array
    {
        $path = resource_path('data/fatigue_management_gmo_program.json');
        if (! is_file($path)) {
            return [];
        }

        $payload = json_decode((string) file_get_contents($path), true);

        return is_array($payload) ? $payload : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function loadPerkuatanData(): array
    {
        $paths = [
            resource_path('data/perkuatan_fatigue_management_piala_dunia_2026.json'),
            resource_path('views/fatigue-management/perkuatan_fatigue_management_piala_dunia_2026.json'),
        ];

        foreach ($paths as $path) {
            if (! is_file($path)) {
                continue;
            }

            $payload = json_decode((string) file_get_contents($path), true);

            return is_array($payload) ? $payload : [];
        }

        return [];
    }
}
