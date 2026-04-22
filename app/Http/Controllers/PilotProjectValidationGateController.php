<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PilotProjectValidationGate;
use App\Models\PilotProjectValidationProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PilotProjectValidationGateController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $rows = PilotProjectValidationGate::query()
            ->with('project:id,project_name')
            ->when($q !== '', fn ($query) => $query->where('gate_label', 'like', "%{$q}%")->orWhere('gate_title', 'like', "%{$q}%"))
            ->orderBy('project_id')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pilot-project-validation.gates.index', compact('rows', 'q'));
    }

    public function create(): View
    {
        $projects = PilotProjectValidationProject::query()->orderBy('project_name')->get(['id', 'project_name']);

        return view('pilot-project-validation.gates.create', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:pilot_project_validation_projects,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'gate_label' => ['required', 'string', 'max:128'],
            'gate_title' => ['nullable', 'string', 'max:255'],
            'gate_caption' => ['nullable', 'string'],
            'hard_gate' => ['nullable', 'boolean'],
            'gate_definition' => ['nullable', 'string'],
            'project_specific_explanation' => ['nullable', 'string'],
            'what_gate_confirms' => ['nullable', 'string'],
            'what_pic_needs_to_fill' => ['nullable', 'string'],
            'pic_status' => ['nullable', 'string', 'max:128'],
            'pic_notes_key_findings' => ['nullable', 'string'],
            'evidence_link_folder' => ['nullable', 'string'],
            'pic_owner' => ['nullable', 'string', 'max:255'],
            'target_close_date' => ['nullable', 'date'],
            'reviewer_status' => ['nullable', 'string', 'max:128'],
        ]);
        $data['hard_gate'] = (bool) ($data['hard_gate'] ?? false);

        PilotProjectValidationGate::query()->create($data);

        return redirect()->route('pilot-project-validation.gates.index')->with('success', 'Gate berhasil dibuat.');
    }

    public function edit(PilotProjectValidationGate $gate): View
    {
        $projects = PilotProjectValidationProject::query()->orderBy('project_name')->get(['id', 'project_name']);

        return view('pilot-project-validation.gates.edit', ['row' => $gate, 'projects' => $projects]);
    }

    public function update(Request $request, PilotProjectValidationGate $gate): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:pilot_project_validation_projects,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'gate_label' => ['required', 'string', 'max:128'],
            'gate_title' => ['nullable', 'string', 'max:255'],
            'gate_caption' => ['nullable', 'string'],
            'hard_gate' => ['nullable', 'boolean'],
            'gate_definition' => ['nullable', 'string'],
            'project_specific_explanation' => ['nullable', 'string'],
            'what_gate_confirms' => ['nullable', 'string'],
            'what_pic_needs_to_fill' => ['nullable', 'string'],
            'pic_status' => ['nullable', 'string', 'max:128'],
            'pic_notes_key_findings' => ['nullable', 'string'],
            'evidence_link_folder' => ['nullable', 'string'],
            'pic_owner' => ['nullable', 'string', 'max:255'],
            'target_close_date' => ['nullable', 'date'],
            'reviewer_status' => ['nullable', 'string', 'max:128'],
        ]);
        $data['hard_gate'] = (bool) ($data['hard_gate'] ?? false);

        $gate->update($data);

        return redirect()->route('pilot-project-validation.gates.index')->with('success', 'Gate berhasil diperbarui.');
    }

    public function destroy(PilotProjectValidationGate $gate): RedirectResponse
    {
        $gate->delete();

        return redirect()->route('pilot-project-validation.gates.index')->with('success', 'Gate berhasil dihapus.');
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:51200'],
        ]);

        $file = $request->file('file');
        if ($file === null) {
            return redirect()->route('pilot-project-validation.gates.index')->with('error', 'File tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load((string) $file->getRealPath());
        $rows = $this->readRowsFromSheet($spreadsheet);
        if ($rows === []) {
            return redirect()->route('pilot-project-validation.gates.index')->with('error', 'Sheet Excel kosong atau header tidak terbaca.');
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
                if ($projectName === '') {
                    $skipped++;
                    continue;
                }

                $projectKey = mb_strtolower($projectName);
                if (! $projectMap->has($projectKey)) {
                    $skipped++;
                    $missingProjects[$projectName] = true;
                    continue;
                }
                $projectId = (int) $projectMap->get($projectKey);

                $gateLabel = trim((string) ($row['gate'] ?? $row['gate_label'] ?? ''));
                if ($gateLabel === '') {
                    $skipped++;
                    continue;
                }

                $sortOrder = $this->parseSortOrder($row['sort_order'] ?? null, $idx);
                $gate = PilotProjectValidationGate::query()->firstOrNew([
                    'project_id' => $projectId,
                    'gate_label' => $gateLabel,
                ]);

                $gate->fill([
                    'sort_order' => $sortOrder,
                    'gate_title' => $this->nullableString($row['gate_title'] ?? null),
                    'hard_gate' => $this->parseBool($row['hard_gate'] ?? null),
                    'gate_definition' => $this->nullableString($row['gate_definition'] ?? null),
                    'project_specific_explanation' => $this->nullableString($row['project_specific_explanation'] ?? null),
                    'what_gate_confirms' => $this->nullableString($row['what_this_gate_confirms'] ?? $row['what_gate_confirms'] ?? null),
                    'what_pic_needs_to_fill' => $this->nullableString($row['what_pic_needs_to_fill'] ?? null),
                    'gate_caption' => $this->nullableString($row['original_caption'] ?? $row['gate_caption'] ?? null),
                    'pic_status' => $this->nullableString($row['pic_status'] ?? null),
                    'pic_notes_key_findings' => $this->nullableString($row['pic_notes_key_findings'] ?? null),
                    'evidence_link_folder' => $this->nullableString($row['evidence_link_folder'] ?? $row['evidence_link'] ?? null),
                    'pic_owner' => $this->nullableString($row['pic_owner'] ?? null),
                    'target_close_date' => $this->parseDate($row['target_close_date'] ?? null),
                    'reviewer_status' => $this->nullableString($row['reviewer_status'] ?? null),
                ]);

                $isNew = ! $gate->exists;
                $gate->save();
                if ($isNew) {
                    $inserted++;
                } else {
                    $updated++;
                }
            }
        });

        $missingProjectText = ($missingProjects !== [])
            ? (' Project tidak ditemukan: ' . implode(', ', array_slice(array_keys($missingProjects), 0, 5)) . (count($missingProjects) > 5 ? '...' : ''))
            : '';

        return redirect()
            ->route('pilot-project-validation.gates.index')
            ->with('success', "Import gates selesai. Insert: {$inserted}, update: {$updated}, skip: {$skipped}.{$missingProjectText}");
    }

    public function downloadTemplateExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GATES');

        $headers = [
            'Project',
            'Gate',
            'Gate Title',
            'Hard Gate',
            'Gate Definition',
            'Project-specific Explanation',
            'What This Gate Confirms',
            'What PIC Needs to Fill',
            'Original Caption',
            'PIC Status',
            'PIC Notes / Key Findings',
            'Evidence Link / Folder',
            'PIC Owner',
            'Target Close Date',
            'Reviewer Status',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            'Remote Dozer',
            'Gate 1',
            'Technical Feasibility',
            'Yes',
            'Menilai kelayakan teknis dasar solusi sebelum pilot dilanjutkan atau diperluas.',
            'Untuk proyek Remote Dozer, gate ini fokus pada network readiness, command latency, and telemetry stability for remote dozing.',
            'Kesiapan infrastruktur, integrasi sistem, kualitas data/sinyal, stabilitas teknis, uptime, latency, dan reliability.',
            'Lengkapi evidence hasil uji teknis, gap teknis terbuka, mitigasi, dan rekomendasi readiness.',
            'Network readiness, command latency, and telemetry stability for remote dozing.',
            'Conditional',
            'Konsep jaringan CPP stockpile secara teknis menjanjikan, namun kesiapan end-to-end penuh masih bersifat bersyarat.',
            '',
            'Automation / IT / Ops / Trakindo / XLSMART',
            '2026-08-15',
            'In Review',
        ], null, 'A2');

        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template-gates.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readRowsFromSheet(Spreadsheet $spreadsheet): array
    {
        $sheet = $spreadsheet->getSheetByName('GATES');
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
            $k = preg_replace('/[%()]/', '', $k) ?? $k;
            $k = preg_replace('/[\s\-\/]+/', '_', $k) ?? $k;
            $k = preg_replace('/_+/', '_', $k) ?? $k;
            return trim($k, '_');
        }, $header);

        $output = [];
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
            foreach ($assoc as $value) {
                if ($value !== null && trim((string) $value) !== '') {
                    $hasValue = true;
                    break;
                }
            }
            if ($hasValue) {
                $output[] = $assoc;
            }
        }

        return $output;
    }

    private function parseSortOrder(mixed $value, int $fallback): int
    {
        if (is_numeric($value)) {
            return max(0, (int) $value);
        }

        return $fallback;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '' || $text === '-') {
            return null;
        }

        return $text;
    }

    private function parseBool(mixed $value): bool
    {
        $text = mb_strtolower(trim((string) ($value ?? '')));

        return in_array($text, ['1', 'true', 'yes', 'ya', 'y', 'hard'], true);
    }

    private function parseDate(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '' || $text === '-') {
            return null;
        }

        $timestamp = strtotime($text);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d', $timestamp);
    }
}

