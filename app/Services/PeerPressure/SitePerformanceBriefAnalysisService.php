<?php

declare(strict_types=1);

namespace App\Services\PeerPressure;

/**
 * Analisis ringkas site_performance.json: gap, perhatian, repetitif, overall (aman / perlu perhatian / waspada).
 */
class SitePerformanceBriefAnalysisService
{
    /** @var list<string> */
    private const GAP_CODES_COUNT = [
        'Incident',
        'Accident',
        'GR',
        'PSPP',
        'Blindspot TBC',
        'Overdue Hazard',
        'True Alert Fatigue',
    ];

    /**
     * @return array<string, mixed>
     */
    public function analyze(): array
    {
        $path = resource_path('data/site_performance.json');
        if (! is_file($path)) {
            return $this->emptyResult('File site_performance.json tidak ditemukan.');
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return $this->emptyResult('Gagal membaca site_performance.json.');
        }
        $rows = json_decode($raw, true);
        if (! is_array($rows) || $rows === []) {
            return $this->emptyResult('Data site performance kosong.');
        }

        $hazard = $this->loadJson('peer_pressure_hazard_reporting_by_site.json');
        $tbc = $this->loadJson('peer_pressure_tbc_high_by_site.json');
        $areaKritis = $this->loadJson('peer_pressure_area_kritis_by_site.json');

        $last = $this->findLatestYearWeek($rows);
        if ($last === null) {
            return $this->emptyResult('Tidak ada baris Year/Week yang valid.');
        }
        [$lastYear, $lastWeek] = $last;

        $lastWeekRows = array_values(array_filter($rows, function ($r) use ($lastYear, $lastWeek) {
            return is_array($r)
                && (int) ($r['Year'] ?? 0) === $lastYear
                && (int) ($r['Week'] ?? 0) === $lastWeek;
        }));

        $gapsLastWeek = [];
        foreach ($lastWeekRows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $gaps = $this->computeGapsForRow($row);
            if ($gaps === []) {
                continue;
            }
            $key = $this->siteMitraKey($row);
            $gapsLastWeek[] = [
                'site' => (string) ($row['Site'] ?? ''),
                'mitra' => (string) ($row['Mitra Kerja'] ?? ''),
                'key' => $key,
                'gaps' => $gaps,
                'gap_count' => count($gaps),
            ];
        }

        $attention = array_values(array_filter($gapsLastWeek, fn (array $g) => $g['gap_count'] > 3));

        $repetitive = $this->findRepetitiveGaps($rows);

        $overallByKey = [];
        foreach ($lastWeekRows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $key = $this->siteMitraKey($row);
            $site = (string) ($row['Site'] ?? '');
            $gaps = $this->computeGapsForRow($row);
            $gapExclIa = $this->countGapsExcludingIncidentAccident($gaps);
            $hStatus = $this->controlStatusHazardTbc($hazard, $site, true);
            $tStatus = $this->controlStatusHazardTbc($tbc, $site, false);
            $areaNeed = $this->areaKritisNeedCount($areaKritis, $site);
            $incident = (int) ($row['Incident'] ?? 0);
            $hasAnyGap = count($gaps) > 0;

            $overallByKey[$key] = $this->classifyOverall(
                $hStatus,
                $tStatus,
                $areaNeed,
                $incident,
                $hasAnyGap,
                $gapExclIa
            );
        }

        $narrative = $this->buildNarrative(
            $lastYear,
            $lastWeek,
            $gapsLastWeek,
            $attention,
            $repetitive,
            $overallByKey
        );

        return [
            'ok' => true,
            'last_year' => $lastYear,
            'last_week' => $lastWeek,
            'gaps_last_week' => $gapsLastWeek,
            'attention_sites' => $attention,
            'repetitive' => $repetitive,
            'overall_by_site_mitra' => $overallByKey,
            'narrative' => $narrative,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyResult(string $message): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'last_year' => null,
            'last_week' => null,
            'gaps_last_week' => [],
            'attention_sites' => [],
            'repetitive' => [],
            'overall_by_site_mitra' => [],
            'narrative' => $message,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadJson(string $filename): ?array
    {
        $path = resource_path('data/'.$filename);
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $d = json_decode($raw, true);

        return is_array($d) ? $d : null;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{0:int,1:int}|null
     */
    private function findLatestYearWeek(array $rows): ?array
    {
        $bestY = 0;
        $bestW = 0;
        foreach ($rows as $r) {
            if (! is_array($r)) {
                continue;
            }
            $y = (int) ($r['Year'] ?? 0);
            $w = (int) ($r['Week'] ?? 0);
            if ($y <= 0 || $w <= 0) {
                continue;
            }
            if ($y > $bestY || ($y === $bestY && $w > $bestW)) {
                $bestY = $y;
                $bestW = $w;
            }
        }

        return $bestY > 0 ? [$bestY, $bestW] : null;
    }

    private function siteMitraKey(array $row): string
    {
        return trim((string) ($row['Site'] ?? '')).'|'.trim((string) ($row['Mitra Kerja'] ?? ''));
    }

    /**
     * Samakan label site ke kunci chart (BMO3 ≈ BMO 3).
     */
    private function normalizeSiteForChart(string $site): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($site)) ?? '');
    }

    /**
     * @param  array<string, mixed>|null  $chart
     * @return 'in'|'under'|'out'
     */
    private function controlStatusHazardTbc(?array $chart, string $site, bool $isHazard): string
    {
        if ($chart === null || empty($chart['bySite']) || ! is_array($chart['bySite'])) {
            return 'under';
        }
        $weeks = $chart['weeks'] ?? ['W12', 'W13', 'W14', 'W15'];
        if (! is_array($weeks) || $weeks === []) {
            return 'under';
        }
        $lastW = $weeks[count($weeks) - 1];
        $norm = $this->normalizeSiteForChart($site);
        $bySite = $chart['bySite'];
        $series = null;
        foreach ($bySite as $label => $wvals) {
            if ($this->normalizeSiteForChart((string) $label) === $norm && is_array($wvals)) {
                $series = $wvals;
                break;
            }
        }
        if ($series === null) {
            return 'under';
        }
        $vals = [];
        foreach ($weeks as $wk) {
            if (isset($series[$wk]) && is_numeric($series[$wk])) {
                $vals[] = (float) $series[$wk];
            }
        }
        if (count($vals) < 2) {
            return 'under';
        }
        $lastVal = (float) ($series[$lastW] ?? $vals[count($vals) - 1]);
        $prior = array_slice($vals, 0, -1);
        if ($prior === []) {
            return 'under';
        }
        $mean = array_sum($prior) / count($prior);
        $var = 0.0;
        foreach ($prior as $p) {
            $var += ($p - $mean) ** 2;
        }
        $var /= count($prior);
        $std = sqrt($var);
        if ($std < 1e-9) {
            return abs($lastVal - $mean) < 1e-6 ? 'in' : 'under';
        }
        $z = abs($lastVal - $mean) / $std;
        if ($z <= 1.0) {
            return 'in';
        }
        if ($z <= 2.0) {
            return 'under';
        }

        return 'out';
    }

    /**
     * Nilai minggu terakhir pada JSON area kritis (label W12…W15 — diselaraskan dengan dashboard).
     *
     * @param  array<string, mixed>|null  $areaJson
     */
    private function areaKritisNeedCount(?array $areaJson, string $site): int
    {
        if ($areaJson === null || empty($areaJson['bySite']) || ! is_array($areaJson['bySite'])) {
            return 0;
        }
        $norm = $this->normalizeSiteForChart($site);
        $weeks = $areaJson['weeks'] ?? [];
        $lastLabel = is_array($weeks) && $weeks !== [] ? (string) $weeks[count($weeks) - 1] : 'W15';
        foreach ($areaJson['bySite'] as $label => $wvals) {
            if ($this->normalizeSiteForChart((string) $label) !== $norm || ! is_array($wvals)) {
                continue;
            }
            if (isset($wvals[$lastLabel])) {
                return (int) $wvals[$lastLabel];
            }
            $last = array_key_last($wvals);

            return $last !== null ? (int) $wvals[$last] : 0;
        }

        return 0;
    }

    /**
     * @param  array<string, bool>  $gaps
     */
    private function countGapsExcludingIncidentAccident(array $gaps): int
    {
        $n = 0;
        foreach ($gaps as $label => $on) {
            if (! $on) {
                continue;
            }
            if ($label === 'Incident' || $label === 'Accident') {
                continue;
            }
            $n++;
        }

        return $n;
    }

    /**
     * @param  array<string, bool>  $gaps
     * @return 'aman'|'perlu_perhatian'|'waspada'
     */
    private function classifyOverall(
        string $hStatus,
        string $tStatus,
        int $areaNeed,
        int $incident,
        bool $hasAnyGap,
        int $gapExclIa
    ): string {
        $hOk = ($hStatus === 'in' || $hStatus === 'under');
        $tOk = ($tStatus === 'in' || $tStatus === 'under');
        $bothIn = ($hStatus === 'in' && $tStatus === 'in');
        $bothUnderNarrow = ($hStatus === 'under' && $tStatus === 'under');
        $anyOut = ($hStatus === 'out' || $tStatus === 'out');

        if ($gapExclIa > 3) {
            return 'waspada';
        }
        if ($anyOut) {
            return 'waspada';
        }

        if ($bothUnderNarrow && $areaNeed > 0 && $incident > 0 && $hasAnyGap) {
            return 'waspada';
        }

        if ($bothIn && $areaNeed === 0 && ! $hasAnyGap) {
            return 'aman';
        }

        if ($hOk && $tOk && $gapExclIa <= 3) {
            return 'perlu_perhatian';
        }

        return 'waspada';
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, bool> label => true
     */
    private function computeGapsForRow(array $row): array
    {
        $gaps = [];

        foreach (self::GAP_CODES_COUNT as $col) {
            $gap = false;
            if ($col === 'Blindspot TBC' || $col === 'Overdue Hazard') {
                $p = $this->parsePercent($row[$col] ?? null);
                $gap = $p !== null && $p > 0.0;
            } else {
                $v = $row[$col] ?? 0;
                $gap = is_numeric($v) && (float) $v != 0.0;
            }
            if ($gap) {
                $gaps[$col] = true;
            }
        }

        $cov = $this->parsePercent($row['Coverage Area Kritis'] ?? null);
        if ($cov !== null && $cov < 100.0) {
            $gaps['Coverage Area Kritis'] = true;
        }

        $pjaBc = $this->parsePercent($row['PJA BC'] ?? null);
        $pjaMk = $this->parsePercent($row['PJA MK'] ?? null);
        if (($pjaBc !== null && $pjaBc < 100.0) || ($pjaMk !== null && $pjaMk < 100.0)) {
            $gaps['PJA BC/MK'] = true;
        }

        $sap = $this->parsePercent($row['Partisipasi Pelaporan SAP L1- L2 MK'] ?? null);
        if ($sap !== null && $sap < 100.0) {
            $gaps['Partisipasi Pelaporan SAP L1- L2 MK'] = true;
        }

        $speak = $this->parsePercent($row['Speak Up Sebelum Alert'] ?? null);
        if ($speak !== null && $speak < 100.0) {
            $gaps['Speak Up Sebelum Alert'] = true;
        }

        return $gaps;
    }

    private function parsePercent(mixed $v): ?float
    {
        if ($v === null) {
            return null;
        }
        if (is_numeric($v)) {
            return (float) $v;
        }
        $s = str_replace(['%', ' '], '', (string) $v);
        $s = str_replace(',', '.', $s);
        if ($s === '' || ! is_numeric($s)) {
            return null;
        }

        return (float) $s;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function findRepetitiveGaps(array $rows): array
    {
        $byGroup = [];
        foreach ($rows as $r) {
            if (! is_array($r)) {
                continue;
            }
            $y = (int) ($r['Year'] ?? 0);
            $w = (int) ($r['Week'] ?? 0);
            if ($y <= 0 || $w <= 0) {
                continue;
            }
            $key = $this->siteMitraKey($r);
            if (! isset($byGroup[$key])) {
                $byGroup[$key] = [];
            }
            $byGroup[$key][] = ['y' => $y, 'w' => $w, 'row' => $r];
        }
        $out = [];
        $seen = [];
        foreach ($byGroup as $key => $list) {
            usort($list, function ($a, $b) {
                $sa = $a['y'] * 100 + $a['w'];
                $sb = $b['y'] * 100 + $b['w'];

                return $sa <=> $sb;
            });
            $n = count($list);
            for ($i = 0; $i < $n - 2; $i++) {
                $a = $list[$i];
                $b = $list[$i + 1];
                $c = $list[$i + 2];
                if ($a['y'] !== $b['y'] || $b['y'] !== $c['y']) {
                    continue;
                }
                if ($b['w'] !== $a['w'] + 1 || $c['w'] !== $b['w'] + 1) {
                    continue;
                }
                $ga = $this->computeGapsForRow($a['row']);
                $gb = $this->computeGapsForRow($b['row']);
                $gc = $this->computeGapsForRow($c['row']);
                foreach ($ga as $label => $true) {
                    if ($true && ! empty($gb[$label]) && ! empty($gc[$label])) {
                        $parts = explode('|', $key);
                        $sig = ($parts[0] ?? '').'|'.($parts[1] ?? '').'|'.$label.'|'.$a['y'].'|'.$a['w'].'-'.$c['w'];
                        if (isset($seen[$sig])) {
                            continue;
                        }
                        $seen[$sig] = true;
                        $out[] = [
                            'site' => $parts[0] ?? '',
                            'mitra' => $parts[1] ?? '',
                            'parameter' => $label,
                            'weeks' => [$a['w'], $b['w'], $c['w']],
                            'year' => $a['y'],
                        ];
                    }
                }
            }
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $gapsLastWeek
     * @param  list<array<string, mixed>>  $attention
     * @param  list<array<string, mixed>>  $repetitive
     * @param  array<string, string>  $overallByKey
     */
    private function buildNarrative(
        int $lastYear,
        int $lastWeek,
        array $gapsLastWeek,
        array $attention,
        array $repetitive,
        array $overallByKey
    ): string {
        $lines = [];
        $lines[] = 'Ringkasan Site Performance (data resources/data/site_performance.json) — minggu terakhir tersedia: '.$lastYear.' minggu '.$lastWeek.'.';
        $lines[] = '';
        $lines[] = 'Aturan gap: angka ≠0 untuk Incident, Accident, GR, PSPP, Blindspot TBC, Overdue Hazard, True Alert Fatigue; persen <100% untuk Coverage Area Kritis, PJA BC/MK, Partisipasi SAP, Speak Up Sebelum Alert.';
        $lines[] = '';

        if ($gapsLastWeek === []) {
            $lines[] = 'Tidak ada gap terdeteksi pada minggu terakhir untuk kombinasi site/mitra di file.';
        } else {
            $lines[] = 'Gap minggu terakhir (hanya yang bermasalah):';
            foreach ($gapsLastWeek as $item) {
                $labels = array_keys($item['gaps']);
                $lines[] = '• '.($item['site'] ?? '').' / '.($item['mitra'] ?? '').': '.implode(', ', $labels).' ('.($item['gap_count'] ?? 0).' parameter).';
            }
        }
        $lines[] = '';

        if ($attention === []) {
            $lines[] = 'Site/mitra perlu perhatian (>3 parameter gap): tidak ada.';
        } else {
            $lines[] = 'Site/mitra perlu perhatian (>3 parameter gap):';
            foreach ($attention as $a) {
                $lines[] = '• '.($a['site'] ?? '').' / '.($a['mitra'] ?? '').' — '.$a['gap_count'].' gap.';
            }
        }
        $lines[] = '';

        if ($repetitive === []) {
            $lines[] = 'Pola repetitif (parameter gap sama 3 minggu berturut-turut): tidak terdeteksi.';
        } else {
            $lines[] = 'Pola repetitif (3 minggu berturut dalam tahun yang sama):';
            foreach ($repetitive as $r) {
                $lines[] = '• '.($r['site'] ?? '').' / '.($r['mitra'] ?? '').' — '.($r['parameter'] ?? '').' (minggu '.implode(', ', $r['weeks'] ?? []).', tahun '.($r['year'] ?? '').').';
            }
        }
        $lines[] = '';

        $cntAman = 0;
        $cntPerlu = 0;
        $cntWas = 0;
        foreach ($overallByKey as $lvl) {
            if ($lvl === 'aman') {
                $cntAman++;
            } elseif ($lvl === 'perlu_perhatian') {
                $cntPerlu++;
            } else {
                $cntWas++;
            }
        }
        $lines[] = 'Overall operasional (heuristik: kontrol Hazard/TBC dari JSON mingguan peer_pressure_*_by_site, area kritis need-to-check, gap & insiden):';
        $lines[] = 'Aman: '.$cntAman.', Perlu perhatian: '.$cntPerlu.', Waspada: '.$cntWas.'.';
        foreach ($overallByKey as $k => $lvl) {
            $lines[] = '• '.$k.' → '.$lvl;
        }

        return implode("\n", $lines);
    }
}
