<?php

declare(strict_types=1);

namespace App\Services\PilotProjectValidation;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Membaca workbook Excel (.xlsx / .xls) dengan sheet PROJECTS, TIMELINE, GATES, METRICS
 * dan menghasilkan payload yang sama dengan struktur frontend (untuk syncPortfolioFromRequest).
 */
class PilotProjectValidationExcelImportService
{
    /**
     * @return array{projects: list<array<string, mixed>>, historySnapshots: list<array<string, mixed>>}
     */
    public function buildPortfolioPayloadFromPath(string $absolutePath): array
    {
        $spreadsheet = IOFactory::load($absolutePath);

        $projectRows = $this->readSheetRows($spreadsheet, 'PROJECTS');
        $timelineRows = $this->readSheetRows($spreadsheet, 'TIMELINE');
        $gateRows = $this->readSheetRows($spreadsheet, 'GATES');
        $metricRows = $this->readSheetRows($spreadsheet, 'METRICS');
        $historyRows = $this->readSheetRows($spreadsheet, 'HISTORY');

        if ($projectRows === [] || $timelineRows === [] || $gateRows === [] || $metricRows === []) {
            throw new InvalidArgumentException(
                'Workbook harus memiliki sheet PROJECTS, TIMELINE, GATES, dan METRICS — masing-masing minimal satu baris data (setelah header).'
            );
        }

        /** @var array<string, array<string, mixed>> $projectMap */
        $projectMap = [];
        /** @var list<string> $order */
        $order = [];

        $ensure = function (mixed $name) use (&$projectMap, &$order): ?string {
            $projectName = trim((string) ($name ?? ''));
            if ($projectName === '') {
                return null;
            }
            if (! isset($projectMap[$projectName])) {
                $projectMap[$projectName] = [
                    'name' => $projectName,
                    'subtitle' => '',
                    'pilotArea' => '',
                    'support' => '',
                    'currentPhase' => '',
                    'progress' => 0,
                    'currentPeriod' => '',
                    'nextMilestone' => '',
                    '_periodMap' => [],
                    '_periodOrder' => [],
                    '_gateMap' => [],
                    '_gateOrder' => [],
                ];
                $order[] = $projectName;
            }

            return $projectName;
        };

        foreach ($projectRows as $row) {
            $key = $ensure($row['project_name'] ?? $row['project'] ?? $row['name'] ?? null);
            if ($key === null) {
                continue;
            }
            $p = &$projectMap[$key];
            $p['subtitle'] = (string) ($row['subtitle'] ?? $p['subtitle']);
            $p['pilotArea'] = (string) ($row['pilot_area'] ?? $row['pilotarea'] ?? $p['pilotArea']);
            $p['support'] = (string) ($row['support'] ?? $p['support']);
            $p['currentPhase'] = (string) ($row['current_phase'] ?? $row['currentphase'] ?? $p['currentPhase']);
            $p['progress'] = $this->clampInt($this->parseNumber($row['progress'] ?? null, (float) $p['progress']), 0, 100);
            $p['currentPeriod'] = (string) ($row['current_period'] ?? $row['currentperiod'] ?? $p['currentPeriod']);
            $p['nextMilestone'] = (string) ($row['next_milestone'] ?? $row['nextmilestone'] ?? $p['nextMilestone']);
            unset($p);
        }

        foreach ($timelineRows as $row) {
            $key = $ensure($row['project_name'] ?? $row['project'] ?? $row['name'] ?? null);
            if ($key === null) {
                continue;
            }
            $p = &$projectMap[$key];
            $period = trim((string) ($row['period'] ?? 'New Period')) ?: 'New Period';
            $phase = trim((string) ($row['phase'] ?? 'New phase')) ?: 'New phase';
            $periodKey = $period . '|||' . $phase;
            if (! isset($p['_periodMap'][$periodKey])) {
                $p['_periodMap'][$periodKey] = [
                    'period' => $period,
                    'phase' => $phase,
                    'status' => $this->normalizeTaskStatus($row['period_status'] ?? $row['status'] ?? 'plan'),
                    'tasks' => [],
                ];
                $p['_periodOrder'][] = $periodKey;
            }
            $p['_periodMap'][$periodKey]['status'] = $this->normalizeTaskStatus($row['period_status'] ?? $p['_periodMap'][$periodKey]['status']);
            $taskText = trim((string) ($row['task_text'] ?? $row['task'] ?? ''));
            if ($taskText !== '') {
                $p['_periodMap'][$periodKey]['tasks'][] = [
                    'text' => $taskText,
                    'owner' => trim((string) ($row['task_owner'] ?? $row['owner'] ?? 'Owner')) ?: 'Owner',
                    'status' => $this->normalizeTaskStatus($row['task_status'] ?? $row['taskstate'] ?? 'plan'),
                ];
            }
            unset($p);
        }

        foreach ($gateRows as $row) {
            $key = $ensure($row['project_name'] ?? $row['project'] ?? $row['name'] ?? null);
            if ($key === null) {
                continue;
            }
            $p = &$projectMap[$key];
            $gateLabel = trim((string) ($row['gate_label'] ?? $row['gate'] ?? 'Gate 1')) ?: 'Gate 1';
            if (! isset($p['_gateMap'][$gateLabel])) {
                $p['_gateMap'][$gateLabel] = [
                    'gate' => $gateLabel,
                    'title' => trim((string) ($row['gate_title'] ?? $row['title'] ?? 'New Gate')) ?: 'New Gate',
                    'caption' => trim((string) ($row['gate_caption'] ?? $row['caption'] ?? '')),
                    'hardGate' => $this->parseBool($row['hard_gate'] ?? null),
                    'metrics' => [],
                ];
                $p['_gateOrder'][] = $gateLabel;
            } else {
                $p['_gateMap'][$gateLabel]['title'] = trim((string) ($row['gate_title'] ?? $p['_gateMap'][$gateLabel]['title'] ?? 'New Gate')) ?: 'New Gate';
                $p['_gateMap'][$gateLabel]['caption'] = trim((string) ($row['gate_caption'] ?? $p['_gateMap'][$gateLabel]['caption'] ?? ''));
                $p['_gateMap'][$gateLabel]['hardGate'] = $this->parseBool($row['hard_gate'] ?? null);
            }
            unset($p);
        }

        foreach ($metricRows as $row) {
            $key = $ensure($row['project_name'] ?? $row['project'] ?? $row['name'] ?? null);
            if ($key === null) {
                continue;
            }
            $p = &$projectMap[$key];
            $gateLabel = trim((string) ($row['gate_label'] ?? $row['gate'] ?? 'Gate 1')) ?: 'Gate 1';
            if (! isset($p['_gateMap'][$gateLabel])) {
                $p['_gateMap'][$gateLabel] = [
                    'gate' => $gateLabel,
                    'title' => $gateLabel,
                    'caption' => '',
                    'hardGate' => false,
                    'metrics' => [],
                ];
                $p['_gateOrder'][] = $gateLabel;
            }
            $metricType = $this->normalizeMetricType($row['metric_type'] ?? $row['type'] ?? 'range');
            $metric = [
                'name' => trim((string) ($row['metric_name'] ?? $row['name'] ?? 'New metric')) ?: 'New metric',
                'desc' => trim((string) ($row['metric_desc'] ?? $row['description'] ?? '')),
                'type' => $metricType,
                'direction' => strtolower(trim((string) ($row['direction'] ?? 'high'))) === 'low' ? 'low' : 'high',
                'unit' => trim((string) ($row['unit'] ?? '%')) ?: '%',
                'critical' => $this->parseBool($row['critical'] ?? null),
            ];
            if ($metricType === 'select') {
                $metric['value'] = $this->normalizeGateDecisionValue($row['current_value'] ?? $row['value'] ?? 'conditional');
            } else {
                $metric['min'] = $this->parseNumber($row['min'] ?? null, 0.0);
                $metric['max'] = $this->parseNumber($row['max'] ?? null, 100.0);
                $metric['step'] = $this->parseNumber($row['step'] ?? null, 1.0);
                $metric['value'] = $this->parseNumber($row['current_value'] ?? $row['value'] ?? null, 50.0);
                $metric['pass'] = $this->parseNumber($row['pass_threshold'] ?? $row['pass'] ?? null, 80.0);
                $metric['conditional'] = $this->parseNumber($row['conditional_threshold'] ?? $row['conditional'] ?? null, 60.0);
            }
            $p['_gateMap'][$gateLabel]['metrics'][] = $metric;
            unset($p);
        }

        $importedProjects = [];
        foreach ($order as $name) {
            $raw = $projectMap[$name];
            $roadmap = [];
            foreach ($raw['_periodOrder'] as $pk) {
                $roadmap[] = $raw['_periodMap'][$pk];
            }
            $gates = [];
            foreach ($raw['_gateOrder'] as $gk) {
                $gates[] = $raw['_gateMap'][$gk];
            }
            $proj = [
                'name' => $raw['name'],
                'subtitle' => $raw['subtitle'],
                'pilotArea' => $raw['pilotArea'],
                'support' => $raw['support'],
                'currentPhase' => $raw['currentPhase'],
                'progress' => $raw['progress'],
                'currentPeriod' => $raw['currentPeriod'],
                'nextMilestone' => $raw['nextMilestone'],
                'roadmap' => $roadmap,
                'gates' => $gates,
            ];
            if ($proj['roadmap'] === []) {
                $proj['roadmap'][] = $this->defaultPeriod();
            }
            foreach ($proj['roadmap'] as $i => $period) {
                if (($period['tasks'] ?? []) === []) {
                    $proj['roadmap'][$i]['tasks'] = [$this->defaultTask()];
                }
            }
            if ($proj['gates'] === []) {
                $proj['gates'][] = $this->defaultGate(0);
            }
            foreach ($proj['gates'] as $gi => $gate) {
                if (($gate['metrics'] ?? []) === []) {
                    $proj['gates'][$gi]['metrics'] = [$this->defaultMetric()];
                }
            }
            $importedProjects[] = $proj;
        }

        $importedHistory = [];
        foreach ($historyRows as $row) {
            $date = trim((string) ($row['date'] ?? $row['period'] ?? ''));
            if ($date === '') {
                continue;
            }
            $scoreRaw = $row['decision_score'] ?? '';
            $score = ($scoreRaw !== '' && $scoreRaw !== null && is_numeric($scoreRaw))
                ? $this->clampInt($scoreRaw, 0, 100)
                : $this->decisionStrengthFromStatus((string) ($row['decision_status'] ?? $row['decision'] ?? $row['status'] ?? ''));

            $importedHistory[] = [
                'date' => $date,
                'projectName' => trim((string) ($row['project_name'] ?? $row['project'] ?? '')),
                'progress' => $this->clampInt($this->parseNumber($row['progress'] ?? null, 0.0), 0, 100),
                'decisionScore' => $this->clampInt($score, 0, 100),
            ];
        }

        return [
            'projects' => $importedProjects,
            'historySnapshots' => $importedHistory,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readSheetRows(Spreadsheet $spreadsheet, string $sheetName): array
    {
        $sheet = $this->findSheet($spreadsheet, $sheetName);
        if ($sheet === null) {
            return [];
        }
        $rows = $sheet->toArray();
        if ($rows === []) {
            return [];
        }
        $header = array_shift($rows);
        if ($header === null) {
            return [];
        }
        $norm = [];
        foreach ($header as $h) {
            $norm[] = $this->normalizeHeaderKey((string) $h);
        }
        $out = [];
        foreach ($rows as $row) {
            $assoc = [];
            foreach ($norm as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $row[$i] ?? null;
            }
            if ($this->rowIsEmpty($assoc)) {
                continue;
            }
            $out[] = $assoc;
        }

        return $out;
    }

    private function findSheet(Spreadsheet $spreadsheet, string $name): ?\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        foreach ($spreadsheet->getWorksheetIterator() as $ws) {
            if (strcasecmp((string) $ws->getTitle(), $name) === 0) {
                return $ws;
            }
        }

        return null;
    }

    private function normalizeHeaderKey(string $key): string
    {
        $key = strtolower(trim($key));

        return preg_replace('/\s+/', '_', $key) ?? $key;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $v) {
            if ($v !== null && $v !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeTaskStatus(mixed $value): string
    {
        $v = strtolower(trim((string) $value));
        if (in_array($v, ['done', 'progress', 'plan', 'risk'], true)) {
            return $v;
        }

        return 'plan';
    }

    private function normalizeMetricType(mixed $value): string
    {
        return strtolower(trim((string) $value)) === 'select' ? 'select' : 'range';
    }

    private function normalizeGateDecisionValue(mixed $value): string
    {
        $v = strtolower(trim((string) $value));
        if (in_array($v, ['pass', 'conditional', 'fail'], true)) {
            return $v;
        }
        if ($v === 'go') {
            return 'pass';
        }
        if ($v === 'conditional go') {
            return 'conditional';
        }
        if (in_array($v, ['no-go', 'nogo', 'fail'], true)) {
            return 'fail';
        }

        return 'conditional';
    }

    private function decisionStrengthFromStatus(string $status): int
    {
        $normalized = strtolower(trim($status));
        if (in_array($normalized, ['go', 'pass'], true)) {
            return 100;
        }
        if (in_array($normalized, ['conditional go', 'conditional'], true)) {
            return 70;
        }
        if (in_array($normalized, ['no-go', 'nogo', 'fail'], true)) {
            return 35;
        }

        return 70;
    }

    private function parseBool(mixed $value): bool
    {
        $v = strtolower(trim((string) ($value ?? '')));

        return in_array($v, ['true', '1', 'yes', 'y'], true);
    }

    private function parseNumber(mixed $value, float $fallback): float
    {
        if ($value === null || $value === '') {
            return $fallback;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        return $fallback;
    }

    private function clampInt(mixed $value, int $min, int $max): int
    {
        if (! is_numeric($value)) {
            return $min;
        }
        $n = (int) round((float) $value);

        return max($min, min($max, $n));
    }

    /**
     * @return array{period: string, phase: string, status: string, tasks: list<array{text: string, owner: string, status: string}>}
     */
    private function defaultPeriod(): array
    {
        return [
            'period' => 'New Period',
            'phase' => 'New phase',
            'status' => 'plan',
            'tasks' => [$this->defaultTask()],
        ];
    }

    /**
     * @return array{text: string, owner: string, status: string}
     */
    private function defaultTask(): array
    {
        return ['text' => 'New task item', 'owner' => 'Owner', 'status' => 'plan'];
    }

    /**
     * @return array{gate: string, title: string, caption: string, hardGate: bool, metrics: list<array<string, mixed>>}
     */
    private function defaultGate(int $idx): array
    {
        return [
            'gate' => 'Gate ' . ($idx + 1),
            'title' => 'New Gate',
            'caption' => 'Describe the purpose of this gate.',
            'hardGate' => false,
            'metrics' => [$this->defaultMetric()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultMetric(): array
    {
        return [
            'name' => 'New metric',
            'desc' => 'Describe the metric logic',
            'type' => 'range',
            'direction' => 'high',
            'unit' => '%',
            'min' => 0.0,
            'max' => 100.0,
            'step' => 1.0,
            'value' => 50.0,
            'pass' => 80.0,
            'conditional' => 60.0,
            'critical' => false,
        ];
    }
}
