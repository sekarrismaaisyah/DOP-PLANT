<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Http\Requests\DopSafety\DopSafetyPlanImportRequest;
use App\Http\Requests\DopSafety\DopSafetyPlanStoreRequest;
use App\Http\Requests\DopSafety\DopSafetyPlanUpdateRequest;
use App\Models\DopOjiPlan;
use App\Services\DopSafety\DopSafetyPlanItemsExcelService;
use App\Services\DopSafety\DopSafetyPlanExcelTemplateService;
use App\Services\DopSafety\DopSafetyPlanImportService;
use App\Services\DopSafety\DopOjiPlanPersistenceService;
use App\Support\DopSafety\DopSafetyProgramDefinition;
use App\Support\DopSafety\DopOjiTableStructure;
use App\Models\DopOjiPlanItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DopOjiPlanController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function __construct(
        private readonly DopOjiPlanPersistenceService $persistenceService,
        private readonly DopSafetyPlanExcelTemplateService $excelTemplateService,
        private readonly DopSafetyPlanItemsExcelService $itemsExcelService,
        private readonly DopSafetyPlanImportService $importService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->get('per_page', 15), [10, 15, 25, 50], true)
            ? (int) $request->get('per_page', 15)
            : 15;

        $query = DopOjiPlan::query()
            ->withCount('items')
            ->with('user:id,name');

        if ($request->filled('site')) {
            $query->where('site', $request->get('site'));
        }

        if ($request->filled('shift')) {
            $query->where('shift', (int) $request->get('shift'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('plan_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('plan_date', '<=', $request->get('date_to'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('site', 'like', "%{$search}%")
                    ->orWhere('created_by_name', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('unit_code', 'like', "%{$search}%")
                            ->orWhere('job_detail', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%");
                    });
            });
        }

        $plans = $query
            ->orderByDesc('plan_date')
            ->orderBy('shift')
            ->paginate($perPage)
            ->withQueryString();

        return view('DopSafety.ojii.index', $this->dopSafetyViewData('oji', [
            'rules' => DopSafetyProgramDefinition::dopDailyRules(),
            'approvers' => ['Dept. Head Plant', 'Dept. Head SHE', 'Supt Safety BC'],
            'plans' => $plans,
            'disclaimer' => config('dop_safety.disclaimer'),
            'statusOptions' => $this->statusOptions(),
        ]));
    }

    public function create(): View
    {
        return view('DopSafety.ojii.create', $this->formViewData('plan', $this->emptyFormDefaults()));
    }

  public function store(DopSafetyPlanStoreRequest $request): RedirectResponse
{
    //dd($request->all(), $request->file());
    $items = $request->itemsPayload();

    foreach ($items as $index => &$item) {

        if ($request->hasFile("evidence_1.$index")) {
            $item['evidence_1'] =
                $request->file("evidence_1.$index")
                    ->store('oji-evidence', 'public');
        }

        if ($request->hasFile("evidence_2.$index")) {
            $item['evidence_2'] =
                $request->file("evidence_2.$index")
                    ->store('oji-evidence', 'public');
        }

        if ($request->hasFile("evidence_3.$index")) {
            $item['evidence_3'] =
                $request->file("evidence_3.$index")
                    ->store('oji-evidence', 'public');
        }

        if ($request->hasFile("evidence_4.$index")) {
            $item['evidence_4'] =
                $request->file("evidence_4.$index")
                    ->store('oji-evidence', 'public');
        }

        if ($request->hasFile("evidence_5.$index")) {
            $item['evidence_5'] =
                $request->file("evidence_5.$index")
                    ->store('oji-evidence', 'public');
        }
    }

    $this->persistenceService->create(
        $request->headerPayload(),
        $items, // ← BUKAN $request->itemsPayload()
        Auth::id(),
    );

    return redirect()
        ->route('dop-safety.oji.index')
        ->with('success', 'DOP berhasil disimpan.');
}

    public function show(DopOjiPlan $plan): View
    {
        $plan->load(['items', 'user:id,name']);

        $itemsBySection = $plan->items->groupBy('section_name');

        return view('DopSafety.ojii.show', $this->dopSafetyViewData('oji', [
            'plan' => $plan,
            'itemsBySection' => $itemsBySection,
            'disclaimer' => config('dop_safety.disclaimer'),
            'watermark' => config('dop_safety.watermark'),
        ]));
    }

    public function edit(DopOjiPlan $plan): View
    {
        $plan->load('items');

        return view('DopSafety.ojii.edit',
            array_merge(
                $this->formViewData(
                    'oji',
                    $this->planToFormDefaults($plan)
                ),
                [
                    'plan' => $plan,
                ]
            )
        );
    }
   
    public function update( DopSafetyPlanUpdateRequest $request,DopOjiPlan $plan): RedirectResponse
    {
        $items = $request->itemsPayload();

        foreach ($items as $index => &$item) {

            foreach (['evidence_1', 'evidence_2', 'evidence_3', 'evidence_4', 'evidence_5'] as $field) {

                if ($request->hasFile($field.'.'.$index)) {

                    $item[$field] = $request
                        ->file($field.'.'.$index)
                        ->store('oji-evidence', 'public');

                }

            }
        }

        unset($item);

        // DEBUG
        //dd($items);

        $this->persistenceService->update(
            $plan,
            $request->headerPayload(),
            $items
        );

        return redirect()
            ->route('dop-safety.oji.show', $plan)
            ->with('success', 'DOP berhasil diperbarui.');
    }

    public function approve(DopOjiPlanItem $item): JsonResponse
    {
        $nextStatus = match ($item->approval_status) {

            'waiting_dept_head' => 'waiting_safety',

            'waiting_safety' => 'waiting_pm',

            'waiting_pm' => 'done',

            default => $item->approval_status,
        };

        $item->update([
            'approval_status' => $nextStatus,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $nextStatus,
        ]);
    }

    public function rejectItem(Request $request,DopOjiPlanItem $item): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $item->update([
            'approval_status' => 'rejected',
            'reject_reason'   => $request->reason,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(DopOjiPlan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()
            ->route('dop-safety.oji.index')
            ->with('success', 'DOP berhasil dihapus.');
    }

    public function downloadTemplate(Request $request): StreamedResponse
    {
        $scope = (string) $request->query('scope', 'document');
        $itemsOnly = $scope === 'items';

        $spreadsheet = $itemsOnly
            ? $this->itemsExcelService->buildSpreadsheet()
            : $this->excelTemplateService->buildSpreadsheet();

        $filename = $itemsOnly
            ? 'template-dop-safety-item-pekerjaan-' . date('Y-m-d') . '.xlsx'
            : 'template-dop-safety-dokumen-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importItems(DopSafetyPlanImportRequest $request): JsonResponse
    {
        $file = $request->file('excel_file');
        if ($file === null) {
            return response()->json([
                'success' => false,
                'message' => 'File Excel tidak ditemukan.',
                'items' => [],
                'errors' => [],
            ], 422);
        }

        try {
            $result = $this->itemsExcelService->parseFromFile($file->getRealPath());
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file Excel: ' . $e->getMessage(),
                'items' => [],
                'errors' => [],
            ], 422);
        }

        if (! empty($result['header_invalid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Struktur kolom Excel tidak sesuai template item pekerjaan.',
                'items' => [],
                'errors' => $result['errors'],
                'header_invalid' => true,
            ], 422);
        }

        if ($result['items'] === []) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada item pekerjaan yang berhasil dibaca.',
                'items' => [],
                'errors' => $result['errors'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => count($result['items']) . ' item pekerjaan berhasil dimuat ke form.',
            'items' => $result['items'],
            'errors' => $result['errors'],
        ]);
    }

    public function import(DopSafetyPlanImportRequest $request): RedirectResponse
    {
        $file = $request->file('excel_file');
        if ($file === null) {
            return back()->with('error', 'File Excel tidak ditemukan.');
        }

        try {
            $result = $this->importService->importFromFile($file->getRealPath());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        if (! empty($result['header_invalid'])) {
            return back()
                ->with('error', 'Upload ditolak: struktur kolom Excel tidak sesuai template DOP Safety.')
                ->with('import_header_errors', $result['errors']);
        }

        if ($result['documents'] === 0 && $result['errors'] !== []) {
            return back()
                ->with('error', 'Import gagal. Periksa error di bawah.')
                ->with('import_errors', $result['errors']);
        }

        $message = "Import berhasil: {$result['documents']} dokumen, {$result['imported']} item pekerjaan.";

        if ($result['errors'] !== []) {
            return back()
                ->with('success', $message)
                ->with('import_errors', $result['errors']);
        }

        return back()->with('success', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function formViewData(string $navActive, array $defaults): array
    {
        return $this->dopSafetyViewData($navActive, [
            'defaults' => $defaults,
            'sectionOptions' => config('dop_safety.sections', []),
            'shiftOptions' => config('dop_safety.shifts', []),
            'statusOptions' => $this->statusOptions(),
            'disclaimer' => config('dop_safety.disclaimer'),
            'tableStructure' => DopOjiTableStructure::definition()['table_structure'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyFormDefaults(): array
    {
        return [
            'plan' => null,
            'site' => config('dop_safety.site', 'GMO'),
            'plan_date' => now()->addDay()->toDateString(),
            'shift' => 1,
            'status' => 'draft',
            'auth_location_date' => '',
            'created_by_name' => Auth::user()?->name ?? '',
            'created_by_position' => '',
            'acknowledged_1_name' => '',
            'acknowledged_1_position' => '',
            'acknowledged_2_name' => '',
            'acknowledged_2_position' => '',
            'acknowledged_3_name' => '',
            'acknowledged_3_position' => '',
            'items' => [$this->emptyItemRow()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function planToFormDefaults(DopOjiPlan $plan): array
    {
        return [
            
            'plan' => $plan,
            'site' => $plan->site,
            'plan_date' => $plan->plan_date->toDateString(),
            'shift' => $plan->shift,
            'status' => $plan->status->value,
            'auth_location_date' => $plan->auth_location_date ?? '',
            'created_by_name' => $plan->created_by_name ?? '',
            'created_by_position' => $plan->created_by_position ?? '',
            'acknowledged_1_name' => $plan->acknowledged_1_name ?? '',
            'acknowledged_1_position' => $plan->acknowledged_1_position ?? '',
            'acknowledged_2_name' => $plan->acknowledged_2_name ?? '',
            'acknowledged_2_position' => $plan->acknowledged_2_position ?? '',
            'acknowledged_3_name' => $plan->acknowledged_3_name ?? '',
            'acknowledged_3_position' => $plan->acknowledged_3_position ?? '',
            'items' => $plan->items->map(function ($item) {
                $workers = DopOjiTableStructure::workersToDisplayCells(is_array($item->workers) ? $item->workers : []);

                return [
                    'id' => $item->id, 
                    'section_name' => $item->section_name,
                    'unit_code' => $item->unit_code,
                    'location' => $item->location,
                    'job_detail' => $item->job_detail,
                    'work_permit' => $item->work_permit,
                    'tools' => is_array($item->tools) ? implode(', ', $item->tools) : '',
                    'worker_names' => $workers['names'],
                    'worker_sids' => $workers['sids'],
                    'cctv' => $item->cctv ?? '',
                    'group_leader' => $item->group_leader ?? '',
                    'group_leader_sid' => $item->group_leader_sid ?? '',
                    'section_head' => $item->section_head ?? '',
                    'section_head_sid' => $item->section_head_sid ?? '',
                    'she_leader' => $item->she_leader ?? '',
                    'she_leader_sid' => $item->she_leader_sid ?? '',
                    'dept_head' => $item->dept_head ?? '',
                    'dept_head_sid' => $item->dept_head_sid ?? '',
                    'pja_bc' => $item->pja_bc ?? '',
                    'evidence_1' => $item->evidence_1,
                    'evidence_2' => $item->evidence_2,
                    'evidence_3' => $item->evidence_3,
                    'evidence_4' => $item->evidence_4,
                    'evidence_5' => $item->evidence_5,
                     'approval_status' => $item->approval_status,
                     'reject_reason' => $item->reject_reason,
                ];
            })->values()->all() ?: [$this->emptyItemRow()],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function emptyItemRow(): array
    {
        return [
            'section_name' => config('dop_safety.sections.0', 'FIELD TRACK'),
            'unit_code' => '',
            'location' => '',
            'job_detail' => '',
            'work_permit' => 'N/A',
            'tools' => '',
            'worker_names' => '',
            'worker_sids' => '',
            'cctv' => '',
            'group_leader' => '',
            'group_leader_sid' => '',
            'section_head' => '',
            'section_head_sid' => '',
            'she_leader' => '',
            'she_leader_sid' => '',
            'dept_head' => '',
            'dept_head_sid' => '',
            'pja_bc' => '',
            
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return collect(\App\Enums\DopSafetyPlanStatus::cases())
            ->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()])
            ->values()
            ->all();
    }

    public function downloadWorkerTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'NRP');
        $sheet->setCellValue('B1', 'NAMA');
        $sheet->setCellValue('C1', 'JABATAN');
        
        $sheet->setCellValue('A2', '412190658');
        $sheet->setCellValue('B2', 'sekar');
        $sheet->setCellValue('C2', 'GL');

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'template_pekerja_mekanik.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function uploadItemWorkers(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => ['required', 'exists:dop_oji_plan_items,id'],
            'file_excel' => ['required', 'mimes:xlsx,xls'],
        ]);

        try {
            $itemId = $request->input('item_id');
            $file = $request->file('file_excel');

            if ($file === null) {
                return response()->json(['success' => false, 'message' => 'File tidak terbaca.'], 422);
            }

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $spreadsheet = $reader->load($file->getRealPath());
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $arrNrp = [];
            $arrName = [];
            $arrPosition = [];
            $workerCount = 0;

            foreach ($sheetData as $rowIndex => $row) {
                if ($rowIndex === 1) {
                    continue; 
                }

                $nrp = trim((string) ($row['A'] ?? ''));
                $name = trim((string) ($row['B'] ?? ''));
                $position = trim((string) ($row['C'] ?? ''));

                if ($nrp === '' && $name === '') {
                    continue;
                }

                $arrNrp[] = $nrp;
                $arrName[] = $name;
                $arrPosition[] = $position !== '' ? $position : 'Mekanik';
                
                $workerCount++;
            }

            \App\Models\DopOjiPlanItemWorker::query()->where('dop_oji_plan_item_id', $itemId)->delete();

            if ($workerCount > 0) {
                \App\Models\DopOjiPlanItemWorker::create([
                    'dop_oji_plan_item_id' => $itemId,
                    'nrp' => implode('; ', $arrNrp),
                    'name' => implode('; ', $arrName),
                    'position' => implode('; ', $arrPosition),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil menggabungkan {$workerCount} pekerja mekanik ke dalam 1 baris.",
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file Excel Pekerja: ' . $e->getMessage(),
            ], 500);
        }
    }


}
