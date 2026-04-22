<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PilotProjectValidationProject;
use App\Models\PilotProjectValidationRoadmapPeriod;
use App\Models\PilotProjectValidationTimelineTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PilotProjectValidationTimelineTaskController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $rows = PilotProjectValidationTimelineTask::query()
            ->with('roadmapPeriod.project:id,project_name')
            ->when($q !== '', fn ($query) => $query->where('task_text', 'like', "%{$q}%"))
            ->orderByRaw('(SELECT project_id FROM pilot_project_validation_roadmap_periods WHERE pilot_project_validation_roadmap_periods.id = pilot_project_validation_timeline_tasks.roadmap_period_id)')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pilot-project-validation.timeline-tasks.index', compact('rows', 'q'));
    }

    public function create(): View
    {
        $periods = PilotProjectValidationRoadmapPeriod::query()
            ->with('project:id,project_name')
            ->orderByDesc('id')
            ->get(['id', 'project_id', 'period', 'phase']);

        return view('pilot-project-validation.timeline-tasks.create', compact('periods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'roadmap_period_id' => ['required', 'integer', 'exists:pilot_project_validation_roadmap_periods,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'task_text' => ['required', 'string'],
            'task_owner' => ['nullable', 'string', 'max:255'],
            'task_status' => ['nullable', 'string', 'max:32'],
            'original_owner' => ['nullable', 'string', 'max:255'],
            'original_status' => ['nullable', 'string', 'max:32'],
            'pic_actual_owner' => ['nullable', 'string', 'max:255'],
            'pic_start_date' => ['nullable', 'date'],
            'pic_actual_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pic_progress_note' => ['nullable', 'string'],
            'evidence_link' => ['nullable', 'string'],
            'target_date' => ['nullable', 'date'],
            'dependency_blocker' => ['nullable', 'string'],
            'task_progress_percent_normalized' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        PilotProjectValidationTimelineTask::query()->create($data);

        return redirect()->route('pilot-project-validation.timeline-tasks.index')->with('success', 'Timeline task berhasil dibuat.');
    }

    public function edit(PilotProjectValidationTimelineTask $timelineTask): View
    {
        $periods = PilotProjectValidationRoadmapPeriod::query()
            ->with('project:id,project_name')
            ->orderByDesc('id')
            ->get(['id', 'project_id', 'period', 'phase']);

        return view('pilot-project-validation.timeline-tasks.edit', ['row' => $timelineTask, 'periods' => $periods]);
    }

    public function update(Request $request, PilotProjectValidationTimelineTask $timelineTask): RedirectResponse
    {
        $data = $request->validate([
            'roadmap_period_id' => ['required', 'integer', 'exists:pilot_project_validation_roadmap_periods,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'task_text' => ['required', 'string'],
            'task_owner' => ['nullable', 'string', 'max:255'],
            'task_status' => ['nullable', 'string', 'max:32'],
            'original_owner' => ['nullable', 'string', 'max:255'],
            'original_status' => ['nullable', 'string', 'max:32'],
            'pic_actual_owner' => ['nullable', 'string', 'max:255'],
            'pic_start_date' => ['nullable', 'date'],
            'pic_actual_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pic_progress_note' => ['nullable', 'string'],
            'evidence_link' => ['nullable', 'string'],
            'target_date' => ['nullable', 'date'],
            'dependency_blocker' => ['nullable', 'string'],
            'task_progress_percent_normalized' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $timelineTask->update($data);

        return redirect()->route('pilot-project-validation.timeline-tasks.index')->with('success', 'Timeline task berhasil diperbarui.');
    }

    public function destroy(PilotProjectValidationTimelineTask $timelineTask): RedirectResponse
    {
        $timelineTask->delete();

        return redirect()->route('pilot-project-validation.timeline-tasks.index')->with('success', 'Timeline task berhasil dihapus.');
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:51200'],
        ]);

        $file = $request->file('file');
        if ($file === null) {
            return redirect()->route('pilot-project-validation.timeline-tasks.index')->with('error', 'File tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load((string) $file->getRealPath());
        $rows = $this->readTaskRows($spreadsheet);
        if ($rows === []) {
            return redirect()->route('pilot-project-validation.timeline-tasks.index')->with('error', 'Sheet Excel kosong atau header tidak terbaca.');
        }

        $projectMap = PilotProjectValidationProject::query()
            ->get(['id', 'project_name'])
            ->mapWithKeys(fn (PilotProjectValidationProject $p): array => [mb_strtolower(trim($p->project_name)) => $p->id]);

        $periodMap = PilotProjectValidationRoadmapPeriod::query()
            ->get(['id', 'project_id', 'period', 'phase'])
            ->mapWithKeys(function (PilotProjectValidationRoadmapPeriod $p): array {
                $key = $p->project_id . '|||' . mb_strtolower(trim((string) $p->period)) . '|||' . mb_strtolower(trim((string) ($p->phase ?? '')));

                return [$key => $p->id];
            });

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $missingProjects = [];
        $missingPeriods = [];

        DB::transaction(function () use ($rows, $projectMap, $periodMap, &$inserted, &$updated, &$skipped, &$missingProjects, &$missingPeriods): void {
            foreach ($rows as $idx => $row) {
                $projectName = trim((string) ($row['project'] ?? $row['project_name'] ?? ''));
                $projectKey = mb_strtolower($projectName);
                if ($projectName === '' || ! $projectMap->has($projectKey)) {
                    $skipped++;
                    if ($projectName !== '') {
                        $missingProjects[$projectName] = true;
                    }
                    continue;
                }
                $projectId = (int) $projectMap->get($projectKey);

                $period = trim((string) ($row['roadmap_period'] ?? $row['period'] ?? ''));
                $phase = trim((string) ($row['phase'] ?? ''));
                $taskText = trim((string) ($row['task'] ?? $row['task_text'] ?? ''));
                if ($period === '' || $taskText === '') {
                    $skipped++;
                    continue;
                }

                $periodKey = $projectId . '|||' . mb_strtolower($period) . '|||' . mb_strtolower($phase);
                if (! $periodMap->has($periodKey)) {
                    $skipped++;
                    $missingPeriods[$projectName . ' - ' . $period . ' - ' . $phase] = true;
                    continue;
                }

                $roadmapPeriodId = (int) $periodMap->get($periodKey);
                $sortOrder = $this->parseSortOrder($row['sort_order'] ?? null, $idx);

                $model = PilotProjectValidationTimelineTask::query()->firstOrNew([
                    'roadmap_period_id' => $roadmapPeriodId,
                    'sort_order' => $sortOrder,
                ]);

                $taskStatus = trim((string) ($row['original_status'] ?? ''));
                if ($taskStatus === '') {
                    $taskStatus = trim((string) ($row['task_status'] ?? ''));
                }
                $taskStatus = $this->normalizeTaskStatus($taskStatus);

                $model->fill([
                    'task_text' => $taskText,
                    'task_owner' => trim((string) ($row['original_owner'] ?? $row['task_owner'] ?? '')),
                    'task_status' => $taskStatus,
                    'original_owner' => trim((string) ($row['original_owner'] ?? '')),
                    'original_status' => $this->normalizeTaskStatus((string) ($row['original_status'] ?? '')),
                    'pic_actual_owner' => $this->normalizeDashText($row['pic_actual_owner'] ?? null),
                    'pic_start_date' => $this->parseOptionalDate((string) ($row['pic_start_date'] ?? '')),
                    'pic_actual_percent' => $this->parseNullablePercent($row['pic_actual_input'] ?? $row['pic_actual'] ?? $row['pic_actual_percent'] ?? null),
                    'pic_progress_note' => $this->normalizeDashText($row['pic_progress_note'] ?? null),
                    'evidence_link' => $this->normalizeDashText($row['evidence_link'] ?? null),
                    'target_date' => $this->parseOptionalDate((string) ($row['target_date'] ?? '')),
                    'dependency_blocker' => $this->normalizeDashText($row['dependency_blocker'] ?? null),
                    'task_progress_percent_normalized' => $this->parseNullablePercent($row['task_progress_normalized'] ?? $row['task_progress'] ?? $row['task_progress_normalized_'] ?? null),
                ]);

                $isNew = ! $model->exists;
                $model->save();
                if ($isNew) {
                    $inserted++;
                } else {
                    $updated++;
                }
            }
        });

        $missingProjectText = ($missingProjects !== []) ? (' Project tidak ditemukan: ' . implode(', ', array_slice(array_keys($missingProjects), 0, 5)) . (count($missingProjects) > 5 ? '...' : '')) : '';
        $missingPeriodText = ($missingPeriods !== []) ? (' Roadmap period tidak ditemukan: ' . implode(' | ', array_slice(array_keys($missingPeriods), 0, 3)) . (count($missingPeriods) > 3 ? '...' : '')) : '';

        return redirect()
            ->route('pilot-project-validation.timeline-tasks.index')
            ->with('success', "Import timeline tasks selesai. Insert: {$inserted}, update: {$updated}, skip: {$skipped}.{$missingProjectText}{$missingPeriodText}");
    }

    public function downloadTemplateExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TIMELINE_TASKS');

        $headers = [
            'Project',
            'Roadmap Period',
            'Phase',
            'Task',
            'Original Owner',
            'Original Status',
            'PIC Actual Owner',
            'PIC Start Date',
            'PIC Actual % Input',
            'PIC Progress Note',
            'Evidence Link',
            'Target Date',
            'Dependency / Blocker',
            'Task Progress % (Normalized)',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            'Remote Pump',
            'Jan-Mar 2026',
            'Remote control commissioning',
            'Commission telemetry and command loop',
            'Automation',
            'Done',
            '-',
            '-',
            '1,0',
            '-',
            '-',
            '-',
            '-',
            '100,0%',
        ], null, 'A2');

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template-timeline-tasks.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readTaskRows(Spreadsheet $spreadsheet): array
    {
        $sheet = $spreadsheet->getSheetByName('TIMELINE_TASKS');
        if ($sheet === null) {
            $sheet = $spreadsheet->getSheet(0);
        }
        $rows = $sheet->toArray();
        if ($rows === []) {
            return [];
        }

        $header = array_shift($rows);
        if (! is_array($header)) {
            return [];
        }

        $keys = array_map(function ($h): string {
            $k = mb_strtolower(trim((string) $h));
            $k = str_replace(['%', '(', ')'], '', $k);
            $k = preg_replace('/[\s\-\/]+/', '_', $k) ?? $k;
            $k = preg_replace('/_+/', '_', $k) ?? $k;

            return trim($k, '_');
        }, $header);

        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $assoc = [];
            foreach ($keys as $i => $k) {
                if ($k === '') {
                    continue;
                }
                $assoc[$k] = $row[$i] ?? null;
            }
            $hasValue = false;
            foreach ($assoc as $v) {
                if ($v !== null && trim((string) $v) !== '') {
                    $hasValue = true;
                    break;
                }
            }
            if ($hasValue) {
                $out[] = $assoc;
            }
        }

        return $out;
    }

    private function parseSortOrder(mixed $value, int $fallback): int
    {
        if (is_numeric($value)) {
            return max(0, (int) $value);
        }

        return $fallback;
    }

    private function parseOptionalDate(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '' || $raw === '-') {
            return null;
        }
        $ts = strtotime($raw);
        if ($ts === false) {
            return null;
        }

        return date('Y-m-d', $ts);
    }

    private function parseNullablePercent(mixed $raw): ?float
    {
        if ($raw === null) {
            return null;
        }
        $s = trim((string) $raw);
        if ($s === '' || $s === '-') {
            return null;
        }
        $s = str_replace('%', '', $s);
        $s = str_replace(',', '.', $s);
        if (! is_numeric($s)) {
            return null;
        }
        $f = (float) $s;

        return max(0.0, min(100.0, round($f, 2)));
    }

    private function normalizeTaskStatus(string $status): string
    {
        $status = mb_strtolower(trim($status));
        if (in_array($status, ['done', 'progress', 'plan', 'risk'], true)) {
            return $status;
        }
        if ($status === '') {
            return 'plan';
        }

        return 'plan';
    }

    private function normalizeDashText(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '' || $text === '-') {
            return null;
        }

        return $text;
    }
}

