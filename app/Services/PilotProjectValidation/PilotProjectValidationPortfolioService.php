<?php

declare(strict_types=1);

namespace App\Services\PilotProjectValidation;

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
                    'progress' => $row->progress,
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
                    'progress' => $this->clampInt($raw['progress'] ?? 0, 0, 100),
                    'current_period' => $this->truncateString((string) ($raw['currentPeriod'] ?? ''), 255),
                    'next_milestone' => $this->truncateString((string) ($raw['nextMilestone'] ?? ''), 16000),
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
                            'period' => $this->truncateString((string) ($periodRow['period'] ?? 'New Period'), 255),
                            'phase' => $this->truncateString((string) ($periodRow['phase'] ?? ''), 255),
                            'status' => $this->normalizePeriodStatus($periodRow['status'] ?? 'plan'),
                        ]);

                        $tasks = $periodRow['tasks'] ?? [];
                        if (is_array($tasks)) {
                            foreach (array_values($tasks) as $tIndex => $taskRow) {
                                if (! is_array($taskRow)) {
                                    continue;
                                }
                                PilotProjectValidationTimelineTask::query()->create([
                                    'roadmap_period_id' => $period->id,
                                    'sort_order' => $tIndex,
                                    'task_text' => $this->truncateString((string) ($taskRow['text'] ?? ''), 16000),
                                    'task_owner' => $this->truncateString((string) ($taskRow['owner'] ?? ''), 255),
                                    'task_status' => $this->normalizeTaskStatus($taskRow['status'] ?? 'plan'),
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
                            'gate_label' => $this->truncateString((string) ($gateRow['gate'] ?? 'Gate ' . ($gIndex + 1)), 128),
                            'gate_title' => $this->truncateString((string) ($gateRow['title'] ?? ''), 255),
                            'gate_caption' => $this->truncateString((string) ($gateRow['caption'] ?? ''), 16000),
                            'hard_gate' => (bool) ($gateRow['hardGate'] ?? false),
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
                                    $metricData['metric_value'] = $this->truncateString($this->numericValueToString($metricRow['value'] ?? 0), 64);
                                    $metricData['min_value'] = $this->nullableFloat($metricRow['min'] ?? null);
                                    $metricData['max_value'] = $this->nullableFloat($metricRow['max'] ?? null);
                                    $metricData['step_value'] = $this->nullableFloat($metricRow['step'] ?? null);
                                    $metricData['pass_threshold'] = $this->nullableFloat($metricRow['pass'] ?? null);
                                    $metricData['conditional_threshold'] = $this->nullableFloat($metricRow['conditional'] ?? null);
                                }
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
                    'progress' => $this->clampInt($histRow['progress'] ?? 0, 0, 100),
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
                'progress' => $this->clampInt($header['progress'] ?? 0, 0, 100),
                'current_period' => (string) ($header['current_period'] ?? 'Current period'),
                'next_milestone' => (string) ($header['next_milestone'] ?? 'Next milestone'),
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
            'progress' => $p->progress,
            'currentPeriod' => $p->current_period ?? '',
            'nextMilestone' => $p->next_milestone ?? '',
            'roadmap' => $p->roadmapPeriods->map(function (PilotProjectValidationRoadmapPeriod $period) {
                return [
                    'period' => $period->period,
                    'phase' => $period->phase ?? '',
                    'status' => $period->status ?? 'plan',
                    'tasks' => $period->tasks->map(function (PilotProjectValidationTimelineTask $t) {
                        return [
                            'text' => $t->task_text,
                            'owner' => $t->task_owner ?? '',
                            'status' => $t->task_status ?? 'plan',
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
                    'metrics' => $g->metrics->map(function (PilotProjectValidationMetric $m) {
                        $base = [
                            'name' => $m->metric_name,
                            'desc' => $m->metric_desc ?? '',
                            'type' => $m->metric_type === 'select' ? 'select' : 'range',
                            'critical' => (bool) $m->critical,
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
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric($value)) {
            return null;
        }
        $f = (float) $value;

        return is_finite($f) ? $f : null;
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
