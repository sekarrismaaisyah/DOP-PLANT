<?php

declare(strict_types=1);

namespace App\Services\PilotProjectValidation;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Membaca workbook Excel (.xlsx / .xls) dengan sheet:
 * - PROJECTS, GATES, METRICS, HISTORY (wajib isi data)
 * - TIMELINE_PERIODS + TIMELINE_TASKS (disarankan, selaras template spreadsheet), atau
 * - TIMELINE (format lama: satu baris = periode + tugas)
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
        $periodRows = $this->readSheetRows($spreadsheet, 'TIMELINE_PERIODS');
        $taskRows = $this->readSheetRows($spreadsheet, 'TIMELINE_TASKS');
        $legacyTimelineRows = $this->readSheetRows($spreadsheet, 'TIMELINE');
        $gateRows = $this->readSheetRows($spreadsheet, 'GATES');
        $metricRows = $this->readSheetRows($spreadsheet, 'METRICS');
        $historyRows = $this->readSheetRows($spreadsheet, 'HISTORY');

        $hasSplitTimeline = $periodRows !== [] || $taskRows !== [];
        $hasLegacyTimeline = $legacyTimelineRows !== [];

        if ($projectRows === [] || $gateRows === [] || $metricRows === []) {
            throw new InvalidArgumentException(
                'Workbook harus memiliki sheet PROJECTS, GATES, dan METRICS — masing-masing minimal satu baris data (setelah header).'
            );
        }

        if (! $hasSplitTimeline && ! $hasLegacyTimeline) {
            throw new InvalidArgumentException(
                'Isi salah satu: sheet TIMELINE (format lama), atau kombinasi TIMELINE_PERIODS / TIMELINE_TASKS (format template spreadsheet).'
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
                    'progress' => 0.0,
                    'currentPeriod' => '',
                    'nextMilestone' => '',
                    'needSupportPic' => '',
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
            $p['subtitle'] = (string) ($row['subtitle'] ?? $row['subtitle_context'] ?? $p['subtitle']);
            $p['pilotArea'] = (string) ($row['pilot_area'] ?? $row['pilotarea'] ?? $p['pilotArea']);
            $p['support'] = (string) ($row['support'] ?? $row['support_needed'] ?? $p['support']);
            $p['currentPhase'] = (string) ($row['current_phase'] ?? $row['currentphase'] ?? $p['currentPhase']);
            $p['progress'] = $this->clampProgress($row['progress'] ?? $p['progress']);
            $p['currentPeriod'] = (string) ($row['current_period'] ?? $row['currentperiod'] ?? $p['currentPeriod']);
            $p['nextMilestone'] = (string) ($row['next_milestone'] ?? $row['nextmilestone'] ?? $p['nextMilestone']);
            $p['needSupportPic'] = (string) ($row['need_support_pic'] ?? $p['needSupportPic']);
            unset($p);
        }

        if ($hasSplitTimeline) {
            $this->importTimelineSplitSheets($projectMap, $ensure, $periodRows, $taskRows);
        } else {
            $this->importTimelineLegacy($projectMap, $ensure, $legacyTimelineRows);
        }

        foreach ($gateRows as $row) {
            $key = $ensure($row['project_name'] ?? $row['project'] ?? $row['name'] ?? null);
            if ($key === null) {
                continue;
            }
            $p = &$projectMap[$key];
            $gateLabel = trim((string) ($row['gate_label'] ?? $row['gate'] ?? 'Gate 1')) ?: 'Gate 1';
            if (! isset($p['_gateMap'][$gateLabel])) {
                $p['_gateMap'][$gateLabel] = $this->emptyGateTemplate($gateLabel, $row);
                $p['_gateOrder'][] = $gateLabel;
            } else {
                $this->mergeGateRowInto($p['_gateMap'][$gateLabel], $row);
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
                $p['_gateMap'][$gateLabel] = $this->emptyGateTemplate($gateLabel, []);
                $p['_gateOrder'][] = $gateLabel;
            }
            $metricType = $this->normalizeMetricType($row['metric_type'] ?? $row['type'] ?? 'range');
            $metric = [
                'name' => trim((string) ($row['metric_name'] ?? $row['name'] ?? 'New metric')) ?: 'New metric',
                'desc' => trim((string) ($row['metric_desc'] ?? $row['metric_description'] ?? $row['description'] ?? '')),
                'type' => $metricType,
                'direction' => strtolower(trim((string) ($row['direction'] ?? 'high'))) === 'low' ? 'low' : 'high',
                'unit' => trim((string) ($row['unit'] ?? '%')) ?: '%',
                'critical' => $this->parseBool($row['critical'] ?? null),
                'picCurrentFinding' => trim((string) ($row['pic_current_finding'] ?? '')),
                'picEvidenceSource' => trim((string) ($row['pic_evidence_source'] ?? '')),
                'picComment' => trim((string) ($row['pic_comment'] ?? '')),
                'metricStatus' => trim((string) ($row['metric_status'] ?? '')),
            ];
            if ($metricType === 'select') {
                $metric['value'] = $this->normalizeGateDecisionValue(
                    $row['metric_value'] ?? $row['current_value'] ?? $row['value'] ?? 'conditional'
                );
            } else {
                $metric['min'] = $this->parseNumber($row['min_value'] ?? $row['min'] ?? null, 0.0);
                $metric['max'] = $this->parseNumber($row['max_value'] ?? $row['max'] ?? null, 100.0);
                $metric['step'] = $this->parseNumber($row['step_value'] ?? $row['step'] ?? null, 1.0);
                $metric['value'] = $this->parseNumber(
                    $row['metric_value'] ?? $row['current_value'] ?? $row['value'] ?? null,
                    50.0
                );
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
                'needSupportPic' => $raw['needSupportPic'],
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
            $date = trim((string) ($row['snapshot_date'] ?? $row['date'] ?? $row['period'] ?? ''));
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
                'progress' => $this->clampProgress($row['progress'] ?? 0),
                'decisionScore' => $this->clampInt($score, 0, 100),
            ];
        }

        return [
            'projects' => $importedProjects,
            'historySnapshots' => $importedHistory,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $projectMap
     * @param  callable(mixed): (?string)  $ensure
     * @param  list<array<string, mixed>>  $periodRows
     * @param  list<array<string, mixed>>  $taskRows
     */
    private function importTimelineSplitSheets(array &$projectMap, callable $ensure, array $periodRows, array $taskRows): void
    {
        foreach ($periodRows as $row) {
            $key = $ensure($row['project_name'] ?? $row['project'] ?? $row['name'] ?? null);
            if ($key === null) {
                continue;
            }
            $p = &$projectMap[$key];
            $rp = trim((string) ($row['roadmap_period'] ?? $row['period'] ?? ''));
            if ($rp === '') {
                $rp = 'New Period';
            }
            $ph = trim((string) ($row['phase'] ?? '')) ?: 'New phase';
            $pkey = $rp . '|||' . $ph;
            if (! isset($p['_periodMap'][$pkey])) {
                $p['_periodMap'][$pkey] = $this->emptyPeriod($rp, $ph);
                $p['_periodOrder'][] = $pkey;
            }
            $ref = &$p['_periodMap'][$pkey];
            $cur = trim((string) ($row['display_current_period'] ?? $row['current_period'] ?? ''));
            if ($cur !== '') {
                $ref['displayCurrentPeriod'] = $cur;
            }
            $ref['status'] = $this->normalizeTaskStatus($row['period_status'] ?? $row['status'] ?? $ref['status']);
            $ref['periodExplanation'] = (string) ($row['period_explanation'] ?? $ref['periodExplanation']);
            $ref['plannedObjectiveOutcome'] = (string) ($row['planned_objective_outcome'] ?? $ref['plannedObjectiveOutcome']);
            $ref['picUpdateSummary'] = (string) ($row['pic_update_summary'] ?? $ref['picUpdateSummary']);
            $ref['picRisksDependencies'] = (string) ($row['pic_risks_dependencies'] ?? $ref['picRisksDependencies']);
            $ref['picOwner'] = (string) ($row['pic_owner'] ?? $ref['picOwner']);
            $td = $this->parseOptionalDateString($row['target_date'] ?? null);
            if ($td !== null) {
                $ref['targetDate'] = $td;
            }
            $ref['reviewerStatus'] = (string) ($row['reviewer_status'] ?? $ref['reviewerStatus']);
            $pp = $this->nullableProgressPercent($row['period_progress_percent'] ?? $row['period_progress'] ?? null);
            if ($pp !== null) {
                $ref['periodProgressPercent'] = $pp;
            }
            unset($ref, $p);
        }

        foreach ($taskRows as $row) {
            $key = $ensure($row['project_name'] ?? $row['project'] ?? $row['name'] ?? null);
            if ($key === null) {
                continue;
            }
            $p = &$projectMap[$key];
            $rp = trim((string) ($row['roadmap_period'] ?? $row['period'] ?? ''));
            if ($rp === '') {
                $rp = 'New Period';
            }
            $ph = trim((string) ($row['phase'] ?? '')) ?: 'New phase';
            $pkey = $rp . '|||' . $ph;
            if (! isset($p['_periodMap'][$pkey])) {
                $p['_periodMap'][$pkey] = $this->emptyPeriod($rp, $ph);
                $p['_periodOrder'][] = $pkey;
            }
            $taskText = trim((string) ($row['task'] ?? $row['task_text'] ?? ''));
            if ($taskText === '') {
                unset($p);
                continue;
            }
            $origOwn = trim((string) ($row['original_owner'] ?? ''));
            $picOwn = trim((string) ($row['pic_actual_owner'] ?? ''));
            $fallbackOwner = trim((string) ($row['task_owner'] ?? $row['owner'] ?? 'Owner')) ?: 'Owner';
            $owner = $picOwn !== '' ? $picOwn : ($origOwn !== '' ? $origOwn : $fallbackOwner);
            $origSt = $this->normalizeTaskStatus($row['original_status'] ?? 'plan');
            $status = $this->normalizeTaskStatus($row['task_status'] ?? $row['status'] ?? $origSt);
            $p['_periodMap'][$pkey]['tasks'][] = [
                'text' => $taskText,
                'owner' => $owner,
                'status' => $status,
                'originalOwner' => $origOwn !== '' ? $origOwn : $owner,
                'originalStatus' => $origSt,
                'picActualOwner' => $picOwn,
                'picStartDate' => $this->parseOptionalDateString($row['pic_start_date'] ?? null) ?? '',
                'picActualPercent' => $this->nullableProgressPercent($row['pic_actual_input'] ?? $row['pic_actual_percent'] ?? null),
                'picProgressNote' => trim((string) ($row['pic_progress_note'] ?? '')),
                'evidenceLink' => trim((string) ($row['evidence_link'] ?? '')),
                'targetDate' => $this->parseOptionalDateString($row['target_date'] ?? null) ?? '',
                'dependencyBlocker' => trim((string) ($row['dependency_blocker'] ?? '')),
                'taskProgressPercentNormalized' => $this->nullableProgressPercent(
                    $row['task_progress_percent_normalized'] ?? $row['task_progress_normalized'] ?? null
                ),
            ];
            unset($p);
        }
    }

    /**
     * @param  array<string, array<string, mixed>>  $projectMap
     * @param  callable(mixed): (?string)  $ensure
     * @param  list<array<string, mixed>>  $timelineRows
     */
    private function importTimelineLegacy(array &$projectMap, callable $ensure, array $timelineRows): void
    {
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
                $p['_periodMap'][$periodKey] = $this->emptyPeriod($period, $phase);
                $p['_periodOrder'][] = $periodKey;
            }
            $ref = &$p['_periodMap'][$periodKey];
            $ref['displayCurrentPeriod'] = (string) ($row['display_current_period'] ?? $row['current_period'] ?? $ref['displayCurrentPeriod']);
            $ref['status'] = $this->normalizeTaskStatus($row['period_status'] ?? $row['status'] ?? $ref['status']);
            $ref['periodExplanation'] = (string) ($row['period_explanation'] ?? $ref['periodExplanation']);
            $ref['plannedObjectiveOutcome'] = (string) ($row['planned_objective_outcome'] ?? $ref['plannedObjectiveOutcome']);
            $ref['picUpdateSummary'] = (string) ($row['pic_update_summary'] ?? $ref['picUpdateSummary']);
            $ref['picRisksDependencies'] = (string) ($row['pic_risks_dependencies'] ?? $ref['picRisksDependencies']);
            $ref['picOwner'] = (string) ($row['pic_owner'] ?? $ref['picOwner']);
            $td = $this->parseOptionalDateString($row['target_date'] ?? null);
            if ($td !== null) {
                $ref['targetDate'] = $td;
            }
            $ref['reviewerStatus'] = (string) ($row['reviewer_status'] ?? $ref['reviewerStatus']);
            $pp = $this->nullableProgressPercent($row['period_progress_percent'] ?? null);
            if ($pp !== null) {
                $ref['periodProgressPercent'] = $pp;
            }
            $taskText = trim((string) ($row['task_text'] ?? $row['task'] ?? ''));
            if ($taskText !== '') {
                $origOwn = trim((string) ($row['original_owner'] ?? ''));
                $picOwn = trim((string) ($row['pic_actual_owner'] ?? ''));
                $fallbackOwner = trim((string) ($row['task_owner'] ?? $row['owner'] ?? 'Owner')) ?: 'Owner';
                $owner = $picOwn !== '' ? $picOwn : ($origOwn !== '' ? $origOwn : $fallbackOwner);
                $origSt = $this->normalizeTaskStatus($row['original_status'] ?? 'plan');
                $status = $this->normalizeTaskStatus($row['task_status'] ?? $row['taskstate'] ?? $origSt);
                $ref['tasks'][] = [
                    'text' => $taskText,
                    'owner' => $owner,
                    'status' => $status,
                    'originalOwner' => $origOwn !== '' ? $origOwn : $owner,
                    'originalStatus' => $origSt,
                    'picActualOwner' => $picOwn,
                    'picStartDate' => $this->parseOptionalDateString($row['pic_start_date'] ?? null) ?? '',
                    'picActualPercent' => $this->nullableProgressPercent($row['pic_actual_percent'] ?? null),
                    'picProgressNote' => trim((string) ($row['pic_progress_note'] ?? '')),
                    'evidenceLink' => trim((string) ($row['evidence_link'] ?? '')),
                    'targetDate' => $this->parseOptionalDateString($row['target_date'] ?? null) ?? '',
                    'dependencyBlocker' => trim((string) ($row['dependency_blocker'] ?? '')),
                    'taskProgressPercentNormalized' => $this->nullableProgressPercent($row['task_progress_percent_normalized'] ?? null),
                ];
            }
            unset($ref, $p);
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{gate: string, title: string, caption: string, hardGate: bool, gateDefinition: string, projectSpecificExplanation: string, whatGateConfirms: string, whatPicNeedsToFill: string, picStatus: string, picNotesKeyFindings: string, evidenceLinkFolder: string, picOwner: string, targetCloseDate: string, reviewerStatus: string, metrics: list<array<string, mixed>>}
     */
    private function emptyGateTemplate(string $gateLabel, array $row): array
    {
        $g = [
            'gate' => $gateLabel,
            'title' => trim((string) ($row['gate_title'] ?? $row['title'] ?? 'New Gate')) ?: 'New Gate',
            'caption' => trim((string) ($row['gate_caption'] ?? $row['caption'] ?? $row['original_caption'] ?? '')),
            'hardGate' => $this->parseBool($row['hard_gate'] ?? null),
            'gateDefinition' => trim((string) ($row['gate_definition'] ?? '')),
            'projectSpecificExplanation' => trim((string) ($row['project_specific_explanation'] ?? '')),
            'whatGateConfirms' => trim((string) ($row['what_this_gate_confirms'] ?? $row['what_gate_confirms'] ?? '')),
            'whatPicNeedsToFill' => trim((string) ($row['what_pic_needs_to_fill'] ?? '')),
            'picStatus' => trim((string) ($row['pic_status'] ?? '')),
            'picNotesKeyFindings' => trim((string) ($row['pic_notes_key_findings'] ?? '')),
            'evidenceLinkFolder' => trim((string) ($row['evidence_link_folder'] ?? '')),
            'picOwner' => trim((string) ($row['pic_owner'] ?? '')),
            'targetCloseDate' => $this->parseOptionalDateString($row['target_close_date'] ?? null) ?? '',
            'reviewerStatus' => trim((string) ($row['reviewer_status'] ?? '')),
            'metrics' => [],
        ];

        return $g;
    }

    /**
     * @param  array<string, mixed>  $gate
     * @param  array<string, mixed>  $row
     */
    private function mergeGateRowInto(array &$gate, array $row): void
    {
        $gate['title'] = trim((string) ($row['gate_title'] ?? $row['title'] ?? $gate['title'])) ?: $gate['title'];
        $cap = trim((string) ($row['gate_caption'] ?? $row['caption'] ?? $row['original_caption'] ?? ''));
        if ($cap !== '') {
            $gate['caption'] = $cap;
        }
        if (array_key_exists('hard_gate', $row) && $row['hard_gate'] !== null && $row['hard_gate'] !== '') {
            $gate['hardGate'] = $this->parseBool($row['hard_gate']);
        }
        foreach ([
            'gateDefinition' => 'gate_definition',
            'projectSpecificExplanation' => 'project_specific_explanation',
            'whatGateConfirms' => ['what_this_gate_confirms', 'what_gate_confirms'],
            'whatPicNeedsToFill' => 'what_pic_needs_to_fill',
            'picStatus' => 'pic_status',
            'picNotesKeyFindings' => 'pic_notes_key_findings',
            'evidenceLinkFolder' => 'evidence_link_folder',
            'picOwner' => 'pic_owner',
            'reviewerStatus' => 'reviewer_status',
        ] as $outKey => $inKeys) {
            $keys = is_array($inKeys) ? $inKeys : [$inKeys];
            foreach ($keys as $ik) {
                $v = trim((string) ($row[$ik] ?? ''));
                if ($v !== '') {
                    $gate[$outKey] = $v;
                    break;
                }
            }
        }
        $tcd = $this->parseOptionalDateString($row['target_close_date'] ?? null);
        if ($tcd !== null) {
            $gate['targetCloseDate'] = $tcd;
        }
    }

    /**
     * @return array{displayCurrentPeriod: string, period: string, phase: string, status: string, periodExplanation: string, plannedObjectiveOutcome: string, picUpdateSummary: string, picRisksDependencies: string, picOwner: string, targetDate: string, reviewerStatus: string, periodProgressPercent: float|null, tasks: list<array<string, mixed>>}
     */
    private function emptyPeriod(string $roadmapPeriod, string $phase): array
    {
        return [
            'displayCurrentPeriod' => '',
            'period' => $roadmapPeriod,
            'phase' => $phase,
            'status' => 'plan',
            'periodExplanation' => '',
            'plannedObjectiveOutcome' => '',
            'picUpdateSummary' => '',
            'picRisksDependencies' => '',
            'picOwner' => '',
            'targetDate' => '',
            'reviewerStatus' => '',
            'periodProgressPercent' => null,
            'tasks' => [],
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
        $key = str_replace(['%', '(', ')'], '', $key);
        $key = preg_replace('/[\s\-\/]+/', '_', $key) ?? $key;
        $key = preg_replace('/_+/', '_', $key) ?? $key;

        return trim($key, '_');
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
        $n = $this->normalizeLocaleNumber($value);
        if ($n === null) {
            return $fallback;
        }
        $f = (float) $n;

        return is_finite($f) ? $f : $fallback;
    }

    private function normalizeLocaleNumber(mixed $value): float|int|string|null
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_bool($value)) {
            return null;
        }
        if (is_numeric($value)) {
            return $value;
        }
        $s = trim((string) $value);
        $s = str_replace(["\u{00A0}", '%'], '', $s);
        $s = trim($s);
        if ($s === '') {
            return null;
        }
        if (str_contains($s, ',') && ! str_contains($s, '.')) {
            $s = str_replace(',', '.', $s);
        } elseif (str_contains($s, ',') && str_contains($s, '.')) {
            $s = str_replace(',', '', $s);
        }

        return is_numeric($s) ? $s : null;
    }

    private function clampProgress(mixed $value): float
    {
        $n = $this->normalizeLocaleNumber($value);
        if ($n === null) {
            return 0.0;
        }
        $f = (float) $n;
        if (! is_finite($f)) {
            return 0.0;
        }

        return max(0.0, min(100.0, round($f, 2)));
    }

    private function nullableProgressPercent(mixed $value): ?float
    {
        $n = $this->normalizeLocaleNumber($value);
        if ($n === null) {
            return null;
        }
        $f = (float) $n;
        if (! is_finite($f)) {
            return null;
        }

        return max(0.0, min(100.0, round($f, 2)));
    }

    private function parseOptionalDateString(mixed $value): ?string
    {
        $s = trim((string) ($value ?? ''));
        if ($s === '') {
            return null;
        }
        try {
            $ts = strtotime($s);

            return $ts !== false ? date('Y-m-d', $ts) : null;
        } catch (\Throwable) {
            return null;
        }
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
     * @return array{displayCurrentPeriod: string, period: string, phase: string, status: string, periodExplanation: string, plannedObjectiveOutcome: string, picUpdateSummary: string, picRisksDependencies: string, picOwner: string, targetDate: string, reviewerStatus: string, periodProgressPercent: null, tasks: list<array<string, mixed>>}
     */
    private function defaultPeriod(): array
    {
        return [
            'displayCurrentPeriod' => '',
            'period' => 'New Period',
            'phase' => 'New phase',
            'status' => 'plan',
            'periodExplanation' => '',
            'plannedObjectiveOutcome' => '',
            'picUpdateSummary' => '',
            'picRisksDependencies' => '',
            'picOwner' => '',
            'targetDate' => '',
            'reviewerStatus' => '',
            'periodProgressPercent' => null,
            'tasks' => [$this->defaultTask()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultTask(): array
    {
        return [
            'text' => 'New task item',
            'owner' => 'Owner',
            'status' => 'plan',
            'originalOwner' => 'Owner',
            'originalStatus' => 'plan',
            'picActualOwner' => '',
            'picStartDate' => '',
            'picActualPercent' => null,
            'picProgressNote' => '',
            'evidenceLink' => '',
            'targetDate' => '',
            'dependencyBlocker' => '',
            'taskProgressPercentNormalized' => null,
        ];
    }

    /**
     * @return array{gate: string, title: string, caption: string, hardGate: bool, gateDefinition: string, projectSpecificExplanation: string, whatGateConfirms: string, whatPicNeedsToFill: string, picStatus: string, picNotesKeyFindings: string, evidenceLinkFolder: string, picOwner: string, targetCloseDate: string, reviewerStatus: string, metrics: list<array<string, mixed>>}
     */
    private function defaultGate(int $idx): array
    {
        return [
            'gate' => 'Gate ' . ($idx + 1),
            'title' => 'New Gate',
            'caption' => 'Describe the purpose of this gate.',
            'hardGate' => false,
            'gateDefinition' => '',
            'projectSpecificExplanation' => '',
            'whatGateConfirms' => '',
            'whatPicNeedsToFill' => '',
            'picStatus' => '',
            'picNotesKeyFindings' => '',
            'evidenceLinkFolder' => '',
            'picOwner' => '',
            'targetCloseDate' => '',
            'reviewerStatus' => '',
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
            'picCurrentFinding' => '',
            'picEvidenceSource' => '',
            'picComment' => '',
            'metricStatus' => '',
        ];
    }
}
