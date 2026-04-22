<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PilotProjectValidationProject;
use App\Models\PilotProjectValidationRoadmapPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PilotProjectValidationRoadmapPeriodController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $rows = PilotProjectValidationRoadmapPeriod::query()
            ->with('project:id,project_name')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('period', 'like', "%{$q}%")
                        ->orWhere('phase', 'like', "%{$q}%")
                        ->orWhere('status', 'like', "%{$q}%");
                });
            })
            ->orderBy('project_id')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pilot-project-validation.roadmap-periods.index', compact('rows', 'q'));
    }

    public function create(): View
    {
        $projects = PilotProjectValidationProject::query()->orderBy('project_name')->get(['id', 'project_name']);

        return view('pilot-project-validation.roadmap-periods.create', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:pilot_project_validation_projects,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'display_current_period' => ['nullable', 'string', 'max:255'],
            'period' => ['required', 'string', 'max:255'],
            'phase' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:32'],
            'period_explanation' => ['nullable', 'string'],
            'planned_objective_outcome' => ['nullable', 'string'],
            'pic_update_summary' => ['nullable', 'string'],
            'pic_risks_dependencies' => ['nullable', 'string'],
            'pic_owner' => ['nullable', 'string', 'max:255'],
            'target_date' => ['nullable', 'date'],
            'reviewer_status' => ['nullable', 'string', 'max:128'],
            'period_progress_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        PilotProjectValidationRoadmapPeriod::query()->create($data);

        return redirect()->route('pilot-project-validation.roadmap-periods.index')->with('success', 'Roadmap period berhasil dibuat.');
    }

    public function edit(PilotProjectValidationRoadmapPeriod $roadmapPeriod): View
    {
        $projects = PilotProjectValidationProject::query()->orderBy('project_name')->get(['id', 'project_name']);

        return view('pilot-project-validation.roadmap-periods.edit', ['row' => $roadmapPeriod, 'projects' => $projects]);
    }

    public function update(Request $request, PilotProjectValidationRoadmapPeriod $roadmapPeriod): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:pilot_project_validation_projects,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'display_current_period' => ['nullable', 'string', 'max:255'],
            'period' => ['required', 'string', 'max:255'],
            'phase' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:32'],
            'period_explanation' => ['nullable', 'string'],
            'planned_objective_outcome' => ['nullable', 'string'],
            'pic_update_summary' => ['nullable', 'string'],
            'pic_risks_dependencies' => ['nullable', 'string'],
            'pic_owner' => ['nullable', 'string', 'max:255'],
            'target_date' => ['nullable', 'date'],
            'reviewer_status' => ['nullable', 'string', 'max:128'],
            'period_progress_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $roadmapPeriod->update($data);

        return redirect()->route('pilot-project-validation.roadmap-periods.index')->with('success', 'Roadmap period berhasil diperbarui.');
    }

    public function destroy(PilotProjectValidationRoadmapPeriod $roadmapPeriod): RedirectResponse
    {
        $roadmapPeriod->delete();

        return redirect()->route('pilot-project-validation.roadmap-periods.index')->with('success', 'Roadmap period berhasil dihapus.');
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:51200'],
        ]);

        $file = $request->file('file');
        if ($file === null) {
            return redirect()->route('pilot-project-validation.roadmap-periods.index')->with('error', 'File tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load((string) $file->getRealPath());
        $rows = $this->readRoadmapRows($spreadsheet);
        if ($rows === []) {
            return redirect()->route('pilot-project-validation.roadmap-periods.index')->with('error', 'Sheet Excel kosong atau header tidak terbaca.');
        }

        $projectMap = PilotProjectValidationProject::query()
            ->get(['id', 'project_name'])
            ->mapWithKeys(fn (PilotProjectValidationProject $p): array => [mb_strtolower(trim($p->project_name)) => $p->id]);

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $missingProjects = [];

        DB::transaction(function () use ($rows, $projectMap, &$inserted, &$updated, &$skipped, &$missingProjects): void {
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

                $period = trim((string) ($row['roadmap_period'] ?? $row['period'] ?? ''));
                $phase = trim((string) ($row['phase'] ?? ''));
                if ($period === '') {
                    $skipped++;
                    continue;
                }

                $projectId = (int) $projectMap->get($projectKey);
                $model = PilotProjectValidationRoadmapPeriod::query()->firstOrNew([
                    'project_id' => $projectId,
                    'period' => $period,
                    'phase' => $phase,
                ]);

                $model->fill([
                    'sort_order' => $this->parseSortOrder($row['sort_order'] ?? null, $idx),
                    'display_current_period' => trim((string) ($row['current_period'] ?? $row['display_current_period'] ?? '')),
                    'status' => trim((string) ($row['period_status'] ?? $row['status'] ?? 'plan')) ?: 'plan',
                    'period_explanation' => (string) ($row['period_explanation'] ?? ''),
                    'planned_objective_outcome' => (string) ($row['planned_objective_outcome'] ?? ''),
                    'pic_update_summary' => (string) ($row['pic_update_summary'] ?? ''),
                    'pic_risks_dependencies' => (string) ($row['pic_risks_dependencies'] ?? ''),
                    'pic_owner' => trim((string) ($row['pic_owner'] ?? '')),
                    'target_date' => $this->parseOptionalDate((string) ($row['target_date'] ?? '')),
                    'reviewer_status' => trim((string) ($row['reviewer_status'] ?? '')),
                    'period_progress_percent' => $this->parseNullablePercent($row['period_progress'] ?? $row['period_progress_percent'] ?? null),
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

        $missing = array_keys($missingProjects);
        $missingNote = $missing !== [] ? (' Project tidak ditemukan: ' . implode(', ', array_slice($missing, 0, 6)) . (count($missing) > 6 ? '...' : '')) : '';

        return redirect()
            ->route('pilot-project-validation.roadmap-periods.index')
            ->with('success', "Import roadmap selesai. Insert: {$inserted}, update: {$updated}, skip: {$skipped}.{$missingNote}");
    }

    public function downloadTemplateExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('ROADMAP_PERIODS');

        $headers = [
            'Project',
            'Current Period',
            'Roadmap Period',
            'Phase',
            'Period Status',
            'Period Explanation',
            'Planned Objective / Outcome',
            'PIC Update Summary',
            'PIC Risks / Dependencies',
            'PIC Owner',
            'Target Date',
            'Reviewer Status',
            'Period Progress %',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            'Arcas HD',
            'Apr - Jun 2026',
            'Jan - Mar 2026',
            'Infrastructure & technical proving',
            'Progress',
            'Fase Infrastructure & technical proving pada periode Jan-Mar 2026 untuk Arcas HD.',
            'Validate network backbone and ROC connection stability; Complete initial end-to-end system integration test',
            'Infrastruktur RTK dan server VPS monitoring siap 24/7.',
            'Menunggu approval final dari tim operasi lapangan.',
            'masbukhin',
            '31-Mar-26',
            'on review',
            '60,0%',
        ], null, 'A2');

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template-roadmap-periods.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readRoadmapRows(Spreadsheet $spreadsheet): array
    {
        $sheet = $spreadsheet->getSheetByName('ROADMAP_PERIODS');
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
            $nonEmpty = false;
            foreach ($assoc as $v) {
                if ($v !== null && trim((string) $v) !== '') {
                    $nonEmpty = true;
                    break;
                }
            }
            if ($nonEmpty) {
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
        if ($raw === '') {
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
        if ($s === '') {
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
}

