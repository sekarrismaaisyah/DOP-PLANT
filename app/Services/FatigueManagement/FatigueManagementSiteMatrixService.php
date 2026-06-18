<?php

declare(strict_types=1);

namespace App\Services\FatigueManagement;

use App\Support\FatigueManagement\FatigueManagementCompanyResolver;

/**
 * Matriks Program (kiri) × Evidence per Site/Mitra (sel) — gaya dashboard HO.
 */
final class FatigueManagementSiteMatrixService
{
    /** @var list<string> */
    private const MATRIX_SITES = ['GMO'];

    /**
     * @return list<string>
     */
    public function matrixSites(): array
    {
        return self::MATRIX_SITES;
    }

    /**
     * @param  list<string>|null  $sites
     * @return list<string>
     */
    public function partnerKeysForSites(?array $sites = null): array
    {
        $sites = $sites ?? self::MATRIX_SITES;
        $siteMap = $this->loadSitePartnerMap();
        $keys = [];

        foreach ($sites as $site) {
            $norm = $this->normalizeSite($site);
            foreach ($siteMap[$norm] ?? [] as $partnerKey) {
                $keys[strtoupper($partnerKey)] = strtoupper($partnerKey);
            }
        }

        return array_values($keys);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    public function build(array $rows): array
    {
        $siteMap = $this->loadSitePartnerMap();
        $columns = $this->buildColumns($siteMap);
        $rowIndex = $this->indexRowsByProgramPartner($rows);
        $programs = $this->uniquePrograms($rows);
        $programGroups = $this->buildProgramGroups($programs, $columns, $rowIndex, $siteMap);
        $riskScores = $this->buildSiteRiskScores($columns, $programGroups);

        return [
            'title' => 'Fatigue Management GMO — Program vs Evidence',
            'subtitle' => 'Site GMO · Hasil evidence per mitra',
            'scope_sites' => self::MATRIX_SITES,
            'columns' => $columns,
            'site_column_groups' => $this->groupColumnsBySite($columns),
            'program_groups' => $programGroups,
            'risk_scores' => $riskScores,
            'legend' => [
                ['icon' => '✓', 'class' => 'fm-mx-ev-legend--ok', 'label' => 'Checklist sesuai frekuensi'],
                ['icon' => '↑', 'class' => 'fm-mx-ev-legend--upload', 'label' => 'Sudah upload, menunggu verifikasi'],
                ['icon' => '✗', 'class' => 'fm-mx-ev-legend--no', 'label' => 'Belum upload evidence'],
                ['icon' => '!', 'class' => 'fm-mx-ev-legend--warn', 'label' => 'Perlu dilengkapi / perbaikan'],
                ['note' => '— = program tidak berlaku untuk mitra/site tersebut'],
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, array<string, mixed>>
     */
    private function indexRowsByProgramPartner(array $rows): array
    {
        $index = [];
        foreach ($rows as $row) {
            $key = (string) ($row['program_key'] ?? '') . '|' . strtoupper((string) ($row['partner_key'] ?? ''));
            $index[$key] = $row;
        }

        return $index;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function uniquePrograms(array $rows): array
    {
        $byKey = [];
        foreach ($rows as $row) {
            $key = (string) ($row['program_key'] ?? '');
            if ($key === '' || isset($byKey[$key])) {
                continue;
            }
            $byKey[$key] = $row;
        }

        $programs = array_values($byKey);
        usort($programs, static function (array $a, array $b): int {
            $freqOrder = ['shift' => 0, 'daily' => 1, 'weekly' => 2];
            $fa = $freqOrder[$a['frequency_category'] ?? 'weekly'] ?? 9;
            $fb = $freqOrder[$b['frequency_category'] ?? 'weekly'] ?? 9;
            if ($fa !== $fb) {
                return $fa <=> $fb;
            }

            $typeOrder = ['mandatory' => 0, 'upgrade' => 1, 'mitra' => 2];
            $ta = $typeOrder[$a['program_type'] ?? 'mitra'] ?? 9;
            $tb = $typeOrder[$b['program_type'] ?? 'mitra'] ?? 9;
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }

            return ($a['program_no'] ?? 0) <=> ($b['program_no'] ?? 0);
        });

        return $programs;
    }

    /**
     * @param  list<array<string, mixed>>  $programs
     * @param  list<array<string, string>>  $columns
     * @param  array<string, array<string, mixed>>  $rowIndex
     * @param  array<string, list<string>>  $siteMap
     * @return list<array<string, mixed>>
     */
    private function buildProgramGroups(
        array $programs,
        array $columns,
        array $rowIndex,
        array $siteMap,
    ): array {
        $groupDefs = [
            'shift' => ['label' => 'Shift', 'bar' => 'orange'],
            'daily' => ['label' => 'Harian', 'bar' => 'green'],
            'weekly' => ['label' => 'Mingguan', 'bar' => 'blue'],
        ];

        $bucketed = ['shift' => [], 'daily' => [], 'weekly' => []];
        foreach ($programs as $program) {
            $category = (string) ($program['frequency_category'] ?? 'weekly');
            if (! isset($bucketed[$category])) {
                $category = 'weekly';
            }

            $cells = [];
            foreach ($columns as $col) {
                $cells[] = $this->evidenceCellForProgram(
                    $program,
                    $col,
                    $rowIndex,
                    $siteMap,
                );
            }

            $bucketed[$category][] = [
                'program_key' => $program['program_key'] ?? '',
                'program_no' => $program['program_no'] ?? 0,
                'title' => $program['program_title'] ?? '',
                'frequency' => $program['frequency_category_label'] ?? ($program['frequency_raw'] ?? ($program['frequency'] ?? '—')),
                'program_type_label' => $program['program_type_label'] ?? '',
                'cells' => $cells,
            ];
        }

        $groups = [];
        foreach ($groupDefs as $key => $def) {
            if ($bucketed[$key] === []) {
                continue;
            }
            $groups[] = [
                'key' => $key,
                'label' => $def['label'],
                'bar' => $def['bar'],
                'programs' => $bucketed[$key],
            ];
        }

        return $groups;
    }

    /**
     * @param  array<string, mixed>  $program
     * @param  array<string, string>  $col
     * @param  array<string, array<string, mixed>>  $rowIndex
     * @param  array<string, list<string>>  $siteMap
     * @return array<string, mixed>
     */
    private function evidenceCellForProgram(
        array $program,
        array $col,
        array $rowIndex,
        array $siteMap,
    ): array {
        $programKey = (string) ($program['program_key'] ?? '');
        $partnerKey = strtoupper((string) ($col['partner_key'] ?? ''));
        $site = (string) ($col['site'] ?? '');

        if (! $this->programAppliesToColumn($program, $site, $partnerKey, $siteMap)) {
            return [
                'status' => 'na',
                'display' => '—',
                'sub' => '',
                'heat' => 'na',
                'empty' => true,
            ];
        }

        $row = $rowIndex[$programKey . '|' . $partnerKey] ?? null;
        if ($row === null) {
            return $this->formatEvidenceCell(null);
        }

        return $this->formatEvidenceCell($row);
    }

    /**
     * @param  array<string, mixed>|null  $row
     * @return array<string, mixed>
     */
    private function formatEvidenceCell(?array $row): array
    {
        if ($row === null) {
            return [
                'status' => 'belum',
                'display' => '✗',
                'sub' => 'Belum Upload',
                'heat' => 'bad',
                'empty' => false,
            ];
        }

        $checklistMet = (bool) ($row['checklist_met'] ?? false);
        $evidenceStatus = (string) ($row['evidence_status'] ?? 'belum_upload');
        $evalStatus = (string) ($row['evaluation_status'] ?? '');

        if ($checklistMet && $evidenceStatus === 'terverifikasi') {
            return [
                'status' => 'verified',
                'display' => '✓',
                'sub' => 'Terverifikasi',
                'heat' => 'excellent',
                'empty' => false,
                'eval' => $evalStatus === 'disetujui' ? 'Disetujui' : '',
            ];
        }

        if ($checklistMet) {
            return [
                'status' => 'checklist',
                'display' => '✓',
                'sub' => $row['checklist_label'] ?? 'Checklist OK',
                'heat' => 'good',
                'empty' => false,
            ];
        }

        if ($evidenceStatus === 'perlu_lengkap') {
            return [
                'status' => 'revision',
                'display' => '!',
                'sub' => 'Perlu Dilengkapi',
                'heat' => 'warn',
                'empty' => false,
            ];
        }

        if ($evidenceStatus === 'sudah_upload') {
            return [
                'status' => 'uploaded',
                'display' => '↑',
                'sub' => $row['evidence_status_label'] ?? 'Sudah Upload',
                'heat' => 'warn',
                'empty' => false,
            ];
        }

        return [
            'status' => 'belum',
            'display' => '✗',
            'sub' => 'Belum Upload',
            'heat' => 'bad',
            'empty' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $program
     * @param  array<string, list<string>>  $siteMap
     */
    private function programAppliesToColumn(array $program, string $site, string $partnerKey, array $siteMap): bool
    {
        $siteNorm = $this->normalizeSite($site);
        $allowed = $siteMap[$siteNorm] ?? [];
        if (! in_array($partnerKey, array_map('strtoupper', $allowed), true)) {
            return false;
        }

        $type = (string) ($program['program_type'] ?? 'mitra');
        if (in_array($type, ['mandatory', 'upgrade'], true)) {
            return true;
        }

        $sites = $program['sites'] ?? [];
        if ($sites === []) {
            return $siteNorm === 'GMO';
        }

        foreach ($sites as $s) {
            if ($this->normalizeSite((string) $s) === $siteNorm) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<array<string, string>>  $columns
     * @param  list<array<string, mixed>>  $programGroups
     * @return list<array<string, mixed>>
     */
    private function buildSiteRiskScores(array $columns, array $programGroups): array
    {
        $bySite = [];
        foreach ($columns as $col) {
            $bySite[$col['site']]['cols'][] = $col;
        }

        $colScores = [];
        foreach ($programGroups as $group) {
            foreach ($group['programs'] as $prog) {
                foreach ($prog['cells'] as $i => $cell) {
                    if ($cell['empty'] ?? false) {
                        continue;
                    }
                    $colScores[$i]['total'] = ($colScores[$i]['total'] ?? 0) + 1;
                    if (in_array($cell['status'] ?? '', ['verified', 'checklist'], true)) {
                        $colScores[$i]['ok'] = ($colScores[$i]['ok'] ?? 0) + 1;
                    } elseif (in_array($cell['status'] ?? '', ['belum', 'revision'], true)) {
                        $colScores[$i]['bad'] = ($colScores[$i]['bad'] ?? 0) + 1;
                    }
                }
            }
        }

        $scores = [];
        foreach ($bySite as $site => $data) {
            $pcts = [];
            $needAttention = 0;
            $caution = 0;

            foreach ($data['cols'] as $col) {
                $globalIdx = null;
                foreach ($columns as $gi => $c) {
                    if ($c['cell_key'] === $col['cell_key']) {
                        $globalIdx = $gi;
                        break;
                    }
                }
                if ($globalIdx === null) {
                    continue;
                }

                $stat = $colScores[$globalIdx] ?? ['total' => 0, 'ok' => 0];
                if (($stat['total'] ?? 0) === 0) {
                    continue;
                }

                $pct = round(100 * (int) ($stat['ok'] ?? 0) / (int) $stat['total'], 1);
                $pcts[] = $pct;

                if ($pct < 40) {
                    $caution++;
                } elseif ($pct < 75) {
                    $needAttention++;
                }
            }

            $avg = $pcts !== [] ? array_sum($pcts) / count($pcts) : 0.0;
            $tier = match (true) {
                $avg >= 75 => 'best',
                $avg >= 40 => 'unstable',
                default => 'high',
            };

            $scores[] = [
                'site' => $site,
                'site_label' => $this->siteLabel($site),
                'avg_checklist' => round($avg, 1),
                'tier' => $tier,
                'tier_label' => match ($tier) {
                    'best' => 'Best Profile',
                    'unstable' => 'Unstable Observed Risk Profile',
                    default => 'High Risk Profile',
                },
                'need_attention' => $needAttention,
                'caution' => $caution,
            ];
        }

        return $scores;
    }

    /**
     * @return array<string, list<string>>
     */
    private function loadSitePartnerMap(): array
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
            if (! is_array($payload)) {
                continue;
            }

            $map = [];
            foreach ($payload['summary']['sites'] ?? [] as $siteRow) {
                $site = $this->normalizeSite((string) ($siteRow['site'] ?? ''));
                $partners = [];
                foreach ($siteRow['partners'] ?? [] as $companyName) {
                    $partners[] = FatigueManagementCompanyResolver::companyToPartner((string) $companyName);
                }
                $map[$site] = array_values(array_unique($partners));
            }

            return $map;
        }

        return ['GMO' => array_keys(FatigueManagementCompanyResolver::PARTNER_TO_COMPANY)];
    }

    /**
     * @param  array<string, list<string>>  $siteMap
     * @return list<array<string, string>>
     */
    private function buildColumns(array $siteMap): array
    {
        $columns = [];
        $seen = [];

        foreach (self::MATRIX_SITES as $site) {
            if (! isset($siteMap[$site])) {
                continue;
            }

            foreach ($siteMap[$site] as $partnerKey) {
                $cellKey = $site . '|' . strtoupper($partnerKey);
                if (isset($seen[$cellKey])) {
                    continue;
                }
                $seen[$cellKey] = true;

                $columns[] = [
                    'site' => $site,
                    'site_label' => $this->siteLabel($site),
                    'partner_key' => strtoupper($partnerKey),
                    'partner_label' => strtoupper($partnerKey),
                    'cell_key' => $cellKey,
                ];
            }
        }

        return $columns;
    }

    private function normalizeSite(string $site): string
    {
        $upper = strtoupper(str_replace(' ', '', trim($site)));

        return match ($upper) {
            'BMO1' => 'BMO1',
            'BMO2' => 'BMO2',
            'BMO-1' => 'BMO-1',
            default => $upper,
        };
    }

    private function siteLabel(string $site): string
    {
        return match ($this->normalizeSite($site)) {
            'BMO1' => 'BMO 1',
            'BMO2' => 'BMO 2',
            'BMO-1' => 'BMO 1 OPP',
            default => $site,
        };
    }

    /**
     * @param  list<array<string, string>>  $columns
     * @return list<array<string, mixed>>
     */
    private function groupColumnsBySite(array $columns): array
    {
        $groups = [];
        foreach ($columns as $col) {
            $site = $col['site'];
            if (! isset($groups[$site])) {
                $groups[$site] = [
                    'site' => $site,
                    'site_label' => $col['site_label'],
                    'partners' => [],
                ];
            }
            $groups[$site]['partners'][] = $col;
        }

        return array_values($groups);
    }
}
