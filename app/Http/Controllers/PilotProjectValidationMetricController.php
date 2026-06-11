<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PilotProjectValidationGate;
use App\Models\PilotProjectValidationMetric;
use App\Models\PilotProjectValidationProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PilotProjectValidationMetricController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $rows = PilotProjectValidationMetric::query()
            ->with('gate.project:id,project_name')
            ->when($q !== '', fn ($query) => $query->where('metric_name', 'like', "%{$q}%"))
            ->orderByRaw('(SELECT project_id FROM pilot_project_validation_gates WHERE pilot_project_validation_gates.id = pilot_project_validation_metrics.gate_id)')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pilot-project-validation.metrics.index', compact('rows', 'q'));
    }

    public function create(): View
    {
        $gates = PilotProjectValidationGate::query()
            ->with('project:id,project_name')
            ->orderByDesc('id')
            ->get(['id', 'project_id', 'gate_label', 'gate_title']);

        return view('pilot-project-validation.metrics.create', compact('gates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:pilot_project_validation_gates,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'metric_name' => ['required', 'string', 'max:255'],
            'metric_type' => ['nullable', 'string', 'max:32'],
            'metric_desc' => ['nullable', 'string'],
            'direction' => ['nullable', 'string', 'max:16'],
            'unit' => ['nullable', 'string', 'max:64'],
            'critical' => ['nullable', 'boolean'],
            'metric_value' => ['nullable', 'string', 'max:64'],
            'min_value' => ['nullable', 'numeric'],
            'max_value' => ['nullable', 'numeric'],
            'step_value' => ['nullable', 'numeric'],
            'pass_threshold' => ['nullable', 'numeric'],
            'conditional_threshold' => ['nullable', 'numeric'],
            'pic_current_finding' => ['nullable', 'string'],
            'pic_evidence_source' => ['nullable', 'string'],
            'pic_comment' => ['nullable', 'string'],
            'metric_status' => ['nullable', 'string', 'max:64'],
        ]);
        $data['critical'] = (bool) ($data['critical'] ?? false);
        $data['direction'] = $this->normalizeMetricDirection($data['direction'] ?? null);

        PilotProjectValidationMetric::query()->create($data);

        return redirect()->route('pilot-project-validation.metrics.index')->with('success', 'Metric berhasil dibuat.');
    }

    public function edit(PilotProjectValidationMetric $metric): View
    {
        $gates = PilotProjectValidationGate::query()
            ->with('project:id,project_name')
            ->orderByDesc('id')
            ->get(['id', 'project_id', 'gate_label', 'gate_title']);

        return view('pilot-project-validation.metrics.edit', ['row' => $metric, 'gates' => $gates]);
    }

    public function update(Request $request, PilotProjectValidationMetric $metric): RedirectResponse
    {
        $data = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:pilot_project_validation_gates,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'metric_name' => ['required', 'string', 'max:255'],
            'metric_type' => ['nullable', 'string', 'max:32'],
            'metric_desc' => ['nullable', 'string'],
            'direction' => ['nullable', 'string', 'max:16'],
            'unit' => ['nullable', 'string', 'max:64'],
            'critical' => ['nullable', 'boolean'],
            'metric_value' => ['nullable', 'string', 'max:64'],
            'min_value' => ['nullable', 'numeric'],
            'max_value' => ['nullable', 'numeric'],
            'step_value' => ['nullable', 'numeric'],
            'pass_threshold' => ['nullable', 'numeric'],
            'conditional_threshold' => ['nullable', 'numeric'],
            'pic_current_finding' => ['nullable', 'string'],
            'pic_evidence_source' => ['nullable', 'string'],
            'pic_comment' => ['nullable', 'string'],
            'metric_status' => ['nullable', 'string', 'max:64'],
        ]);
        $data['critical'] = (bool) ($data['critical'] ?? false);
        $data['direction'] = $this->normalizeMetricDirection($data['direction'] ?? null);

        $metric->update($data);

        return redirect()->route('pilot-project-validation.metrics.index')->with('success', 'Metric berhasil diperbarui.');
    }

    public function destroy(PilotProjectValidationMetric $metric): RedirectResponse
    {
        $metric->delete();

        return redirect()->route('pilot-project-validation.metrics.index')->with('success', 'Metric berhasil dihapus.');
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:51200'],
        ]);

        $file = $request->file('file');
        if ($file === null) {
            return redirect()->route('pilot-project-validation.metrics.index')->with('error', 'File tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load((string) $file->getRealPath());
        $rows = $this->readRowsFromSheet($spreadsheet);
        if ($rows === []) {
            return redirect()->route('pilot-project-validation.metrics.index')->with('error', 'Sheet Excel kosong atau header tidak terbaca.');
        }

        $projectMap = PilotProjectValidationProject::query()
            ->get(['id', 'project_name'])
            ->mapWithKeys(fn (PilotProjectValidationProject $p): array => [mb_strtolower(trim($p->project_name)) => $p->id]);

        $gateMap = PilotProjectValidationGate::query()
            ->get(['id', 'project_id', 'gate_label', 'gate_title'])
            ->mapWithKeys(function (PilotProjectValidationGate $gate): array {
                $key = $gate->project_id . '|||' . mb_strtolower(trim((string) $gate->gate_label));
                $titleKey = $key . '|||' . mb_strtolower(trim((string) ($gate->gate_title ?? '')));
                return [$key => $gate->id, $titleKey => $gate->id];
            });

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $missingProjects = [];
        $missingGates = [];

        DB::transaction(function () use ($rows, $projectMap, $gateMap, &$inserted, &$updated, &$skipped, &$missingProjects, &$missingGates): void {
            foreach ($rows as $idx => $row) {
                $projectName = trim((string) ($row['project'] ?? ''));
                $projectKey = mb_strtolower($projectName);
                if ($projectName === '' || ! $projectMap->has($projectKey)) {
                    $skipped++;
                    if ($projectName !== '') {
                        $missingProjects[$projectName] = true;
                    }
                    continue;
                }
                $projectId = (int) $projectMap->get($projectKey);

                $gateLabel = trim((string) ($row['gate'] ?? $row['gate_label'] ?? ''));
                $gateTitle = trim((string) ($row['gate_title'] ?? ''));
                if ($gateLabel === '') {
                    $skipped++;
                    continue;
                }

                $gateLookupKey = $projectId . '|||' . mb_strtolower($gateLabel);
                $gateId = $gateMap->get($gateLookupKey);
                if ($gateTitle !== '') {
                    $gateId = $gateMap->get($gateLookupKey . '|||' . mb_strtolower($gateTitle), $gateId);
                }
                if ($gateId === null) {
                    $skipped++;
                    $missingGates[$projectName . ' - ' . $gateLabel] = true;
                    continue;
                }

                $metricName = trim((string) ($row['metric_name'] ?? ''));
                if ($metricName === '') {
                    $skipped++;
                    continue;
                }

                $sortOrder = $this->parseSortOrder($row['sort_order'] ?? null, $idx);
                $metric = PilotProjectValidationMetric::query()->firstOrNew([
                    'gate_id' => (int) $gateId,
                    'metric_name' => $metricName,
                ]);

                $metric->fill([
                    'sort_order' => $sortOrder,
                    'metric_desc' => $this->nullableString($row['metric_description'] ?? $row['metric_desc'] ?? null),
                    'metric_type' => $this->normalizeMetricType($row['metric_type'] ?? null),
                    'direction' => $this->normalizeMetricDirection($row['direction'] ?? null),
                    'critical' => $this->parseBool($row['critical'] ?? null),
                    'unit' => $this->nullableString($row['unit'] ?? null),
                    'metric_value' => $this->nullableString($row['current_value'] ?? $row['metric_value'] ?? null),
                    'pass_threshold' => $this->parseDecimal($row['pass_threshold'] ?? null),
                    'conditional_threshold' => $this->parseDecimal($row['conditional_threshold'] ?? null),
                    'min_value' => $this->parseDecimal($row['min'] ?? $row['min_value'] ?? null),
                    'max_value' => $this->parseDecimal($row['max'] ?? $row['max_value'] ?? null),
                    'step_value' => $this->parseDecimal($row['step'] ?? $row['step_value'] ?? null),
                    'pic_current_finding' => $this->nullableString($row['pic_current_finding'] ?? null),
                    'pic_evidence_source' => $this->nullableString($row['pic_evidence_source'] ?? null),
                    'pic_comment' => $this->nullableString($row['pic_comment'] ?? null),
                    'metric_status' => $this->nullableString($row['metric_status'] ?? null),
                ]);

                $isNew = ! $metric->exists;
                $metric->save();
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
        $missingGateText = ($missingGates !== [])
            ? (' Gate tidak ditemukan: ' . implode(' | ', array_slice(array_keys($missingGates), 0, 3)) . (count($missingGates) > 3 ? '...' : ''))
            : '';

        return redirect()
            ->route('pilot-project-validation.metrics.index')
            ->with('success', "Import metrics selesai. Insert: {$inserted}, update: {$updated}, skip: {$skipped}.{$missingProjectText}{$missingGateText}");
    }

    public function downloadTemplateExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('METRICS');

        $headers = [
            'Project',
            'Gate',
            'Gate Title',
            'Metric Name',
            'Metric Description',
            'Metric Type',
            'Direction',
            'Critical',
            'Unit',
            'Current Value',
            'Pass Threshold',
            'Conditional Threshold',
            'Min',
            'Max',
            'Step',
            'PIC Current Finding',
            'PIC Evidence / Source',
            'PIC Comment',
            'Metric Status',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            'Remote Dozer',
            'Gate 1',
            'Technical Feasibility',
            'Uptime jaringan',
            'Ketersediaan jaringan yang digunakan untuk kendali remote dozer.',
            'range',
            'high',
            'Yes',
            '%',
            '99',
            '98',
            '96',
            '80',
            '100',
            '0,1',
            'Desain target menggunakan private network dengan 2 BTS untuk mengurangi gangguan sinyal akibat tertutup stockpile.',
            '',
            'Status masih bersyarat sampai log uptime aktual tersedia.',
            'Not Started',
        ], null, 'A2');

        foreach (range('A', 'S') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template-metrics.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readRowsFromSheet(Spreadsheet $spreadsheet): array
    {
        $sheet = $spreadsheet->getSheetByName('METRICS');
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
            foreach ($assoc as $value) {
                if ($value !== null && trim((string) $value) !== '') {
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

    private function parseBool(mixed $value): bool
    {
        $text = mb_strtolower(trim((string) ($value ?? '')));
        return in_array($text, ['1', 'true', 'yes', 'ya', 'y'], true);
    }

    private function parseDecimal(mixed $value): ?float
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '' || $text === '-') {
            return null;
        }
        $text = str_replace('%', '', $text);
        $text = str_replace(',', '.', $text);
        if (! is_numeric($text)) {
            return null;
        }

        return (float) $text;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '' || $text === '-') {
            return null;
        }

        return $text;
    }

    private function normalizeMetricType(mixed $value): string
    {
        $text = mb_strtolower(trim((string) ($value ?? '')));
        if (in_array($text, ['range', 'number', 'text'], true)) {
            return $text;
        }

        return 'range';
    }

    private function normalizeMetricDirection(mixed $value): ?string
    {
        $text = mb_strtolower(trim((string) ($value ?? '')));
        if ($text === '') {
            return null;
        }

        return $text === 'low' ? 'low' : 'high';
    }
}

