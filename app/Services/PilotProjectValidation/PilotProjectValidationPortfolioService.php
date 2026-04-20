<?php

declare(strict_types=1);

namespace App\Services\PilotProjectValidation;

use Carbon\Carbon;
use App\Models\PilotProjectValidationGate;
use App\Models\PilotProjectValidationHistorySnapshot;
use App\Models\PilotProjectValidationMetric;
use App\Models\PilotProjectValidationProject;
use App\Models\PilotProjectValidationRoadmapPeriod;
use App\Models\PilotProjectValidationTimelineTask;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PilotProjectValidationPortfolioService
{
    /** @var list<string> */
    private array $syncUsedProjectNames = [];

    /**
     * @return array{projects: array<int, array<string, mixed>>, historySnapshots: array<int, array<string, mixed>>}
     */
    public function portfolioToFrontendArray(): array
    {
        $projects = PilotProjectValidationProject::query()
            ->with([
                'roadmapPeriods.tasks',
                'gates.metrics',
            ])
            ->orderBy('id')
            ->get();

        $historySnapshots = PilotProjectValidationHistorySnapshot::query()
            ->with('project')
            ->orderBy('id')
            ->get()
            ->map(static function (PilotProjectValidationHistorySnapshot $row): array {
                return [
                    'date' => $row->snapshot_date,
                    'projectName' => $row->project?->project_name ?? '',
                    'progress' => $row->progress !== null ? (float) $row->progress : 0.0,
                    'decisionScore' => $row->decision_score,
                ];
            })
            ->values()
            ->all();

        return [
            'projects' => $projects->map(fn (PilotProjectValidationProject $p) => $this->projectToFrontendShape($p))->values()->all(),
            'historySnapshots' => $historySnapshots,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function syncPortfolioFromRequest(array $payload): void
    {
        $projects = Arr::get($payload, 'projects', []);
        if (! is_array($projects)) {
            $projects = [];
        }
        $history = Arr::get($payload, 'historySnapshots', []);
        if (! is_array($history)) {
            $history = [];
        }

        DB::transaction(function () use ($projects, $history): void {
            $this->syncUsedProjectNames = [];
            PilotProjectValidationProject::query()->delete();

            foreach (array_values($projects) as $pIndex => $raw) {
                if (! is_array($raw)) {
                    continue;
                }
                $name = $this->truncateString(trim((string) ($raw['name'] ?? '')), 255);
                if ($name === '') {
                    continue;
                }
                $name = $this->allocateUniqueProjectName($name);

                $project = PilotProjectValidationProject::query()->create([
                    'project_name' => $name,
                    'subtitle' => $this->truncateString((string) ($raw['subtitle'] ?? ''), 16000),
                    'pilot_area' => $this->truncateString((string) ($raw['pilotArea'] ?? ''), 512),
                    'support' => $this->truncateString((string) ($raw['support'] ?? ''), 16000),
                    'current_phase' => $this->truncateString((string) ($raw['currentPhase'] ?? ''), 255),
                    'progress' => $this->clampProgress($raw['progress'] ?? 0),
                    'current_period' => $this->truncateString((string) ($raw['currentPeriod'] ?? ''), 255),
                    'next_milestone' => $this->truncateString((string) ($raw['nextMilestone'] ?? ''), 16000),
                    'need_support_pic' => $this->truncateString((string) ($raw['needSupportPic'] ?? $raw['need_support_pic'] ?? ''), 255),
                ]);

                $roadmap = $raw['roadmap'] ?? [];
                if (is_array($roadmap)) {
                    foreach (array_values($roadmap) as $rIndex => $periodRow) {
                        if (! is_array($periodRow)) {
                            continue;
                        }
                        $period = PilotProjectValidationRoadmapPeriod::query()->create([
                            'project_id' => $project->id,
                            'sort_order' => $rIndex,
                            'display_current_period' => $this->truncateString((string) ($periodRow['displayCurrentPeriod'] ?? $periodRow['currentPeriod'] ?? ''), 255),
                            'period' => $this->truncateString((string) ($periodRow['period'] ?? $periodRow['roadmapPeriod'] ?? 'New Period'), 255),
                            'phase' => $this->truncateString((string) ($periodRow['phase'] ?? ''), 255),
                            'status' => $this->normalizePeriodStatus($periodRow['status'] ?? $periodRow['periodStatus'] ?? 'plan'),
                            'period_explanation' => $this->truncateString((string) ($periodRow['periodExplanation'] ?? ''), 16000),
                            'planned_objective_outcome' => $this->truncateString((string) ($periodRow['plannedObjectiveOutcome'] ?? ''), 16000),
                            'pic_update_summary' => $this->truncateString((string) ($periodRow['picUpdateSummary'] ?? ''), 16000),
                            'pic_risks_dependencies' => $this->truncateString((string) ($periodRow['picRisksDependencies'] ?? ''), 16000),
                            'pic_owner' => $this->truncateString((string) ($periodRow['picOwner'] ?? ''), 255),
                            'target_date' => $this->parseOptionalDate($periodRow['targetDate'] ?? null),
                            'reviewer_status' => $this->truncateString((string) ($periodRow['reviewerStatus'] ?? ''), 128),
                            'period_progress_percent' => $this->nullableProgressPercent($periodRow['periodProgressPercent'] ?? null),
                        ]);

                        $tasks = $periodRow['tasks'] ?? [];
                        if (is_array($tasks)) {
                            foreach (array_values($tasks) as $tIndex => $taskRow) {
                                if (! is_array($taskRow)) {
                                    continue;
                                }
                                $owner = $this->truncateString((string) ($taskRow['owner'] ?? ''), 255);
                                $status = $this->normalizeTaskStatus($taskRow['status'] ?? 'plan');
                                PilotProjectValidationTimelineTask::query()->create([
                                    'roadmap_period_id' => $period->id,
                                    'sort_order' => $tIndex,
                                    'task_text' => $this->truncateString((string) ($taskRow['text'] ?? $taskRow['task'] ?? ''), 16000),
                                    'task_owner' => $owner,
                                    'task_status' => $status,
                                    'original_owner' => $this->truncateString((string) ($taskRow['originalOwner'] ?? $owner), 255),
                                    'original_status' => $this->truncateString((string) ($taskRow['originalStatus'] ?? $status), 32),
                                    'pic_actual_owner' => $this->truncateString((string) ($taskRow['picActualOwner'] ?? ''), 255),
                                    'pic_start_date' => $this->parseOptionalDate($taskRow['picStartDate'] ?? null),
                                    'pic_actual_percent' => $this->nullableProgressPercent($taskRow['picActualPercent'] ?? null),
                                    'pic_progress_note' => $this->truncateString((string) ($taskRow['picProgressNote'] ?? ''), 16000),
                                    'evidence_link' => $this->truncateString((string) ($taskRow['evidenceLink'] ?? ''), 16000),
                                    'target_date' => $this->parseOptionalDate($taskRow['targetDate'] ?? null),
                                    'dependency_blocker' => $this->truncateString((string) ($taskRow['dependencyBlocker'] ?? ''), 16000),
                                    'task_progress_percent_normalized' => $this->nullableProgressPercent($taskRow['taskProgressPercentNormalized'] ?? null),
                                ]);
                            }
                        }
                    }
                }

                $gates = $raw['gates'] ?? [];
                if (is_array($gates)) {
                    foreach (array_values($gates) as $gIndex => $gateRow) {
                        if (! is_array($gateRow)) {
                            continue;
                        }
                        $gate = PilotProjectValidationGate::query()->create([
                            'project_id' => $project->id,
                            'sort_order' => $gIndex,
                            'gate_label' => $this->truncateString((string) ($gateRow['gate'] ?? $gateRow['gateLabel'] ?? 'Gate ' . ($gIndex + 1)), 128),
                            'gate_title' => $this->truncateString((string) ($gateRow['title'] ?? $gateRow['gateTitle'] ?? ''), 255),
                            'gate_caption' => $this->truncateString((string) ($gateRow['caption'] ?? $gateRow['originalCaption'] ?? ''), 16000),
                            'hard_gate' => $this->parseHardGate($gateRow['hardGate'] ?? $gateRow['hard_gate'] ?? false),
                            'gate_definition' => $this->truncateString((string) ($gateRow['gateDefinition'] ?? ''), 16000),
                            'project_specific_explanation' => $this->truncateString((string) ($gateRow['projectSpecificExplanation'] ?? ''), 16000),
                            'what_gate_confirms' => $this->truncateString((string) ($gateRow['whatGateConfirms'] ?? ''), 16000),
                            'what_pic_needs_to_fill' => $this->truncateString((string) ($gateRow['whatPicNeedsToFill'] ?? ''), 16000),
                            'pic_status' => $this->truncateString((string) ($gateRow['picStatus'] ?? ''), 128),
                            'pic_notes_key_findings' => $this->truncateString((string) ($gateRow['picNotesKeyFindings'] ?? ''), 16000),
                            'evidence_link_folder' => $this->truncateString((string) ($gateRow['evidenceLinkFolder'] ?? ''), 16000),
                            'pic_owner' => $this->truncateString((string) ($gateRow['picOwner'] ?? ''), 255),
                            'target_close_date' => $this->parseOptionalDate($gateRow['targetCloseDate'] ?? null),
                            'reviewer_status' => $this->truncateString((string) ($gateRow['reviewerStatus'] ?? ''), 128),
                        ]);

                        $metrics = $gateRow['metrics'] ?? [];
                        if (is_array($metrics)) {
                            foreach (array_values($metrics) as $mIndex => $metricRow) {
                                if (! is_array($metricRow)) {
                                    continue;
                                }
                                $type = strtolower((string) ($metricRow['type'] ?? 'range')) === 'select' ? 'select' : 'range';
                                $metricData = [
                                    'gate_id' => $gate->id,
                                    'sort_order' => $mIndex,
                                    'metric_name' => $this->truncateString((string) ($metricRow['name'] ?? 'Metric'), 255),
                                    'metric_type' => $this->truncateString($type, 32),
                                    'metric_desc' => $this->truncateString((string) ($metricRow['desc'] ?? ''), 16000),
                                    'direction' => $this->truncateString((string) ($metricRow['direction'] ?? 'high'), 16),
                                    'unit' => $this->truncateString((string) ($metricRow['unit'] ?? ''), 64),
                                    'critical' => (bool) ($metricRow['critical'] ?? false),
                                    'metric_value' => null,
                                    'min_value' => null,
                                    'max_value' => null,
                                    'step_value' => null,
                                    'pass_threshold' => null,
                                    'conditional_threshold' => null,
                                ];
                                if ($type === 'select') {
                                    $metricData['metric_value'] = $this->truncateString($this->normalizeSelectValue($metricRow['value'] ?? 'conditional'), 64);
                                } else {
                                    $metricData['metric_value'] = $this->truncateString($this->numericValueToString($metricRow['value'] ?? $metricRow['currentValue'] ?? 0), 64);
                                    $metricData['min_value'] = $this->nullableFloat($metricRow['min'] ?? null);
                                    $metricData['max_value'] = $this->nullableFloat($metricRow['max'] ?? null);
                                    $metricData['step_value'] = $this->nullableFloat($metricRow['step'] ?? null);
                                    $metricData['pass_threshold'] = $this->nullableFloat($metricRow['pass'] ?? $metricRow['passThreshold'] ?? null);
                                    $metricData['conditional_threshold'] = $this->nullableFloat($metricRow['conditional'] ?? $metricRow['conditionalThreshold'] ?? null);
                                }
                                $metricData['pic_current_finding'] = $this->truncateString((string) ($metricRow['picCurrentFinding'] ?? ''), 16000);
                                $metricData['pic_evidence_source'] = $this->truncateString((string) ($metricRow['picEvidenceSource'] ?? ''), 16000);
                                $metricData['pic_comment'] = $this->truncateString((string) ($metricRow['picComment'] ?? ''), 16000);
                                $metricData['metric_status'] = $this->truncateString((string) ($metricRow['metricStatus'] ?? ''), 64);
                                PilotProjectValidationMetric::query()->create($metricData);
                            }
                        }
                    }
                }

                $this->ensureMinimumProjectStructure($project);
            }

            $projectByName = PilotProjectValidationProject::query()->pluck('id', 'project_name');

            foreach (array_values($history) as $hIndex => $histRow) {
                if (! is_array($histRow)) {
                    continue;
                }
                $pName = trim((string) ($histRow['projectName'] ?? ''));
                if ($pName === '' || ! $projectByName->has($pName)) {
                    continue;
                }
                $pid = (int) $projectByName->get($pName);
                $score = $histRow['decisionScore'] ?? null;
                $decisionScore = is_numeric($score)
                    ? $this->clampInt($score, 0, 100)
                    : 70;

                PilotProjectValidationHistorySnapshot::query()->create([
                    'project_id' => $pid,
                    'sort_order' => $hIndex,
                    'snapshot_date' => $this->truncateString((string) ($histRow['date'] ?? ''), 128),
                    'progress' => $this->clampProgress($histRow['progress'] ?? 0),
                    'decision_score' => $decisionScore,
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $header
     */
    public function createProjectWithDefaults(array $header): PilotProjectValidationProject
    {
        return DB::transaction(function () use ($header): PilotProjectValidationProject {
            $project = PilotProjectValidationProject::query()->create([
                'project_name' => (string) $header['project_name'],
                'subtitle' => (string) ($header['subtitle'] ?? ''),
                'pilot_area' => (string) ($header['pilot_area'] ?? 'Pilot Area'),
                'support' => (string) ($header['support'] ?? 'Support requirement'),
                'current_phase' => (string) ($header['current_phase'] ?? 'New phase'),
                'progress' => $this->clampProgress($header['progress'] ?? 0),
                'current_period' => (string) ($header['current_period'] ?? 'Current period'),
                'next_milestone' => (string) ($header['next_milestone'] ?? 'Next milestone'),
                'need_support_pic' => $this->truncateString((string) ($header['need_support_pic'] ?? ''), 255),
            ]);

            for ($g = 0; $g < 4; $g++) {
                $gate = PilotProjectValidationGate::query()->create([
                    'project_id' => $project->id,
                    'sort_order' => $g,
                    'gate_label' => 'Gate ' . ($g + 1),
                    'gate_title' => 'New Gate',
                    'gate_caption' => 'Describe the purpose of this gate.',
                    'hard_gate' => $g === 0 || $g === 2,
                ]);
                PilotProjectValidationMetric::query()->create([
                    'gate_id' => $gate->id,
                    'sort_order' => 0,
                    'metric_name' => 'New metric',
                    'metric_type' => 'range',
                    'metric_desc' => 'Describe the metric logic',
                    'direction' => 'high',
                    'unit' => '%',
                    'critical' => false,
                    'metric_value' => '50',
                    'min_value' => 0,
                    'max_value' => 100,
                    'step_value' => 1,
                    'pass_threshold' => 80,
                    'conditional_threshold' => 60,
                ]);
            }

            $period = PilotProjectValidationRoadmapPeriod::query()->create([
                'project_id' => $project->id,
                'sort_order' => 0,
                'period' => 'New Period',
                'phase' => 'New phase',
                'status' => 'plan',
            ]);
            PilotProjectValidationTimelineTask::query()->create([
                'roadmap_period_id' => $period->id,
                'sort_order' => 0,
                'task_text' => 'New task item',
                'task_owner' => 'Owner',
                'task_status' => 'plan',
            ]);

            return $project;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function projectToFrontendShape(PilotProjectValidationProject $p): array
    {
        return [
            'name' => $p->project_name,
            'subtitle' => $p->subtitle ?? '',
            'pilotArea' => $p->pilot_area ?? '',
            'support' => $p->support ?? '',
            'currentPhase' => $p->current_phase ?? '',
            'progress' => $p->progress !== null ? (float) $p->progress : 0.0,
            'currentPeriod' => $p->current_period ?? '',
            'nextMilestone' => $p->next_milestone ?? '',
            'needSupportPic' => $p->need_support_pic ?? '',
            'roadmap' => $p->roadmapPeriods->map(function (PilotProjectValidationRoadmapPeriod $period) {
                return [
                    'displayCurrentPeriod' => $period->display_current_period ?? '',
                    'period' => $period->period,
                    'phase' => $period->phase ?? '',
                    'status' => $period->status ?? 'plan',
                    'periodExplanation' => $period->period_explanation ?? '',
                    'plannedObjectiveOutcome' => $period->planned_objective_outcome ?? '',
                    'picUpdateSummary' => $period->pic_update_summary ?? '',
                    'picRisksDependencies' => $period->pic_risks_dependencies ?? '',
                    'picOwner' => $period->pic_owner ?? '',
                    'targetDate' => $period->target_date?->format('Y-m-d') ?? '',
                    'reviewerStatus' => $period->reviewer_status ?? '',
                    'periodProgressPercent' => $period->period_progress_percent !== null ? (float) $period->period_progress_percent : null,
                    'tasks' => $period->tasks->map(function (PilotProjectValidationTimelineTask $t) {
                        return [
                            'text' => $t->task_text,
                            'owner' => $t->task_owner ?? '',
                            'status' => $t->task_status ?? 'plan',
                            'originalOwner' => $t->original_owner ?? $t->task_owner ?? '',
                            'originalStatus' => $t->original_status ?? $t->task_status ?? 'plan',
                            'picActualOwner' => $t->pic_actual_owner ?? '',
                            'picStartDate' => $t->pic_start_date?->format('Y-m-d') ?? '',
                            'picActualPercent' => $t->pic_actual_percent !== null ? (float) $t->pic_actual_percent : null,
                            'picProgressNote' => $t->pic_progress_note ?? '',
                            'evidenceLink' => $t->evidence_link ?? '',
                            'targetDate' => $t->target_date?->format('Y-m-d') ?? '',
                            'dependencyBlocker' => $t->dependency_blocker ?? '',
                            'taskProgressPercentNormalized' => $t->task_progress_percent_normalized !== null ? (float) $t->task_progress_percent_normalized : null,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
            'gates' => $p->gates->map(function (PilotProjectValidationGate $g) {
                return [
                    'gate' => $g->gate_label,
                    'title' => $g->gate_title ?? '',
                    'caption' => $g->gate_caption ?? '',
                    'hardGate' => (bool) $g->hard_gate,
                    'gateDefinition' => $g->gate_definition ?? '',
                    'projectSpecificExplanation' => $g->project_specific_explanation ?? '',
                    'whatGateConfirms' => $g->what_gate_confirms ?? '',
                    'whatPicNeedsToFill' => $g->what_pic_needs_to_fill ?? '',
                    'picStatus' => $g->pic_status ?? '',
                    'picNotesKeyFindings' => $g->pic_notes_key_findings ?? '',
                    'evidenceLinkFolder' => $g->evidence_link_folder ?? '',
                    'picOwner' => $g->pic_owner ?? '',
                    'targetCloseDate' => $g->target_close_date?->format('Y-m-d') ?? '',
                    'reviewerStatus' => $g->reviewer_status ?? '',
                    'metrics' => $g->metrics->map(function (PilotProjectValidationMetric $m) {
                        $base = [
                            'name' => $m->metric_name,
                            'desc' => $m->metric_desc ?? '',
                            'type' => $m->metric_type === 'select' ? 'select' : 'range',
                            'critical' => (bool) $m->critical,
                            'picCurrentFinding' => $m->pic_current_finding ?? '',
                            'picEvidenceSource' => $m->pic_evidence_source ?? '',
                            'picComment' => $m->pic_comment ?? '',
                            'metricStatus' => $m->metric_status ?? '',
                        ];
                        if ($m->metric_type === 'select') {
                            $base['value'] = $this->normalizeSelectValue($m->metric_value ?? 'conditional');

                            return $base;
                        }
                        $base['direction'] = ($m->direction ?? 'high') === 'low' ? 'low' : 'high';
                        $base['unit'] = $m->unit ?? '%';
                        $base['min'] = $m->min_value !== null ? (float) $m->min_value : 0.0;
                        $base['max'] = $m->max_value !== null ? (float) $m->max_value : 100.0;
                        $base['step'] = $m->step_value !== null ? (float) $m->step_value : 1.0;
                        $base['value'] = $this->parseFloatish($m->metric_value ?? '0');
                        $base['pass'] = $m->pass_threshold !== null ? (float) $m->pass_threshold : 80.0;
                        $base['conditional'] = $m->conditional_threshold !== null ? (float) $m->conditional_threshold : 60.0;

                        return $base;
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    private function normalizePeriodStatus(mixed $value): string
    {
        $v = strtolower(trim((string) $value));
        if (in_array($v, ['done', 'progress', 'plan', 'risk'], true)) {
            return $v;
        }

        return 'plan';
    }

    private function normalizeTaskStatus(mixed $value): string
    {
        $v = strtolower(trim((string) $value));
        if (in_array($v, ['done', 'progress', 'plan', 'risk'], true)) {
            return $v;
        }

        return 'plan';
    }

    private function normalizeSelectValue(mixed $value): string
    {
        $v = strtolower(trim((string) $value));
        if (in_array($v, ['pass', 'conditional', 'fail'], true)) {
            return $v;
        }

        return 'conditional';
    }

    private function numericValueToString(mixed $value): string
    {
        if (is_numeric($value)) {
            return (string) $value;
        }

        return '0';
    }

    private function nullableFloat(mixed $value): ?float
    {
        $normalized = $this->normalizeLocaleNumber($value);
        if ($normalized === null) {
            return null;
        }
        $f = (float) $normalized;

        return is_finite($f) ? $f : null;
    }

    /**
     * Angka desimal seperti "62,5" atau "90,1 %" (locale ID).
     */
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
        $f = $this->nullableFloat($value);
        if ($f === null) {
            return null;
        }

        return max(0.0, min(100.0, round($f, 2)));
    }

    private function parseOptionalDate(mixed $value): ?string
    {
        $s = trim((string) ($value ?? ''));
        if ($s === '') {
            return null;
        }
        try {
            return Carbon::parse($s)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseHardGate(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $v = strtolower(trim((string) $value));

        return in_array($v, ['true', '1', 'yes', 'y', 'hard'], true);
    }

    private function truncateString(string $value, int $maxBytes): string
    {
        if ($maxBytes <= 0) {
            return '';
        }
        $value = trim($value);
        if (mb_strlen($value) <= $maxBytes) {
            return $value;
        }

        return mb_substr($value, 0, $maxBytes);
    }

    private function allocateUniqueProjectName(string $base): string
    {
        $base = $this->truncateString($base, 255);
        $candidate = $base;
        $n = 2;
        while (in_array($candidate, $this->syncUsedProjectNames, true)) {
            $suffix = ' (' . $n . ')';
            $stem = mb_substr($base, 0, max(1, 255 - mb_strlen($suffix)));
            $candidate = $this->truncateString($stem . $suffix, 255);
            $n++;
        }
        $this->syncUsedProjectNames[] = $candidate;

        return $candidate;
    }

    private function parseFloatish(?string $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (! is_numeric($value)) {
            return 0.0;
        }

        return (float) $value;
    }

    private function clampInt(mixed $value, int $min, int $max): int
    {
        $n = is_numeric($value) ? (int) round((float) $value) : 0;

        return max($min, min($max, $n));
    }

    private function ensureMinimumProjectStructure(PilotProjectValidationProject $project): void
    {
        if (! $project->roadmapPeriods()->exists()) {
            $period = PilotProjectValidationRoadmapPeriod::query()->create([
                'project_id' => $project->id,
                'sort_order' => 0,
                'period' => 'New Period',
                'phase' => 'New phase',
                'status' => 'plan',
            ]);
            PilotProjectValidationTimelineTask::query()->create([
                'roadmap_period_id' => $period->id,
                'sort_order' => 0,
                'task_text' => 'New task item',
                'task_owner' => 'Owner',
                'task_status' => 'plan',
            ]);
        }

        if (! $project->gates()->exists()) {
            $gate = PilotProjectValidationGate::query()->create([
                'project_id' => $project->id,
                'sort_order' => 0,
                'gate_label' => 'Gate 1',
                'gate_title' => 'New Gate',
                'gate_caption' => '',
                'hard_gate' => true,
            ]);
            PilotProjectValidationMetric::query()->create([
                'gate_id' => $gate->id,
                'sort_order' => 0,
                'metric_name' => 'New metric',
                'metric_type' => 'range',
                'metric_desc' => '',
                'direction' => 'high',
                'unit' => '%',
                'critical' => false,
                'metric_value' => '50',
                'min_value' => 0,
                'max_value' => 100,
                'step_value' => 1,
                'pass_threshold' => 80,
                'conditional_threshold' => 60,
            ]);
        }
    }
}
