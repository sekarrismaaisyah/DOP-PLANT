<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Http\Requests\DopSafety\DopSafetyPlanImportRequest;
use App\Http\Requests\DopSafety\DopSafetyPlanStoreRequest;
use App\Http\Requests\DopSafety\DopSafetyPlanUpdateRequest;
use App\Models\DopSafetyPlan;
use App\Services\DopSafety\DopSafetyPlanExcelTemplateService;
use App\Services\DopSafety\DopSafetyPlanImportService;
use App\Services\DopSafety\DopSafetyPlanPersistenceService;
use App\Support\DopSafety\DopSafetyProgramDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DopSafetyPlanController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function __construct(
        private readonly DopSafetyPlanPersistenceService $persistenceService,
        private readonly DopSafetyPlanExcelTemplateService $excelTemplateService,
        private readonly DopSafetyPlanImportService $importService,
    ) {}

    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->get('per_page', 15), [10, 15, 25, 50], true)
            ? (int) $request->get('per_page', 15)
            : 15;

        $query = DopSafetyPlan::query()
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

        return view('DopSafety.plan.index', $this->dopSafetyViewData('plan', [
            'rules' => DopSafetyProgramDefinition::dopDailyRules(),
            'approvers' => ['Dept. Head Plant', 'Dept. Head SHE', 'Supt Safety BC'],
            'plans' => $plans,
            'disclaimer' => config('dop_safety.disclaimer'),
            'statusOptions' => $this->statusOptions(),
        ]));
    }

    public function create(): View
    {
        return view('DopSafety.plan.create', $this->formViewData('plan', $this->emptyFormDefaults()));
    }

    public function store(DopSafetyPlanStoreRequest $request): RedirectResponse
    {
        $this->persistenceService->create(
            $request->headerPayload(),
            $request->itemsPayload(),
            Auth::id(),
        );

        return redirect()
            ->route('dop-safety.plan.index')
            ->with('success', 'DOP berhasil disimpan.');
    }

    public function show(DopSafetyPlan $plan): View
    {
        $plan->load(['items', 'user:id,name']);

        $itemsBySection = $plan->items->groupBy('section_name');

        return view('DopSafety.plan.show', $this->dopSafetyViewData('plan', [
            'plan' => $plan,
            'itemsBySection' => $itemsBySection,
            'disclaimer' => config('dop_safety.disclaimer'),
            'watermark' => config('dop_safety.watermark'),
        ]));
    }

    public function edit(DopSafetyPlan $plan): View
    {
        $plan->load('items');

        return view('DopSafety.plan.edit', $this->formViewData('plan', $this->planToFormDefaults($plan)));
    }

    public function update(DopSafetyPlanUpdateRequest $request, DopSafetyPlan $plan): RedirectResponse
    {
        $this->persistenceService->update(
            $plan,
            $request->headerPayload(),
            $request->itemsPayload(),
        );

        return redirect()
            ->route('dop-safety.plan.show', $plan)
            ->with('success', 'DOP berhasil diperbarui.');
    }

    public function destroy(DopSafetyPlan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()
            ->route('dop-safety.plan.index')
            ->with('success', 'DOP berhasil dihapus.');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = $this->excelTemplateService->buildSpreadsheet();
        $filename = 'template-dop-safety-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
            'unitCategories' => config('dop_safety.unit_categories', []),
            'shiftOptions' => config('dop_safety.shifts', []),
            'statusOptions' => $this->statusOptions(),
            'disclaimer' => config('dop_safety.disclaimer'),
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
    private function planToFormDefaults(DopSafetyPlan $plan): array
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
            'items' => $plan->items->map(fn ($item) => [
                'section_name' => $item->section_name,
                'unit_code' => $item->unit_code,
                'unit_category' => $item->unit_category,
                'location' => $item->location,
                'job_detail' => $item->job_detail,
                'work_permit' => $item->work_permit,
                'tools' => is_array($item->tools) ? implode(', ', $item->tools) : '',
                'workers' => is_array($item->workers) ? implode(', ', $item->workers) : '',
                'cctv' => $item->cctv ?? '',
                'group_leader' => $item->group_leader ?? '',
                'section_head' => $item->section_head ?? '',
                'she_leader' => $item->she_leader ?? '',
                'dept_head' => $item->dept_head ?? '',
                'pja_bc' => $item->pja_bc ?? '',
            ])->values()->all() ?: [$this->emptyItemRow()],
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
            'unit_category' => 'TRACK',
            'location' => '',
            'job_detail' => '',
            'work_permit' => 'N/A',
            'tools' => '',
            'workers' => '',
            'cctv' => '',
            'group_leader' => '',
            'section_head' => '',
            'she_leader' => '',
            'dept_head' => '',
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
}
