<?php

declare(strict_types=1);

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PembatasanLV\Concerns\ProvidesPembatasanLVInputasiFormContext;
use App\Http\Controllers\PembatasanLV\Concerns\ProvidesPembatasanLVLayout;
use App\Http\Requests\PembatasanLV\PembatasanLVPlanningLvRequest;
use App\Http\Requests\PembatasanLV\PembatasanLVPlanningOrangRequest;
use App\Models\PembatasanLvPlanning;
use App\Models\PembatasanOrangPlanning;
use App\Services\PembatasanLV\PembatasanLVControlRoomContextService;
use App\Services\PembatasanLV\PembatasanLVPlanningCheckinService;
use App\Services\PembatasanLV\PembatasanLVShiftService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PembatasanLVPlanningController extends Controller
{
    use ProvidesPembatasanLVLayout;
    use ProvidesPembatasanLVInputasiFormContext;

    /** @var list<string> */
    private const LV_TEMPLATE_HEADERS = [
        'Tanggal Plan',
        'Shift',
        'Status',
        'Nama Driver',
        'Driver Ref',
        'No Unit',
        'ID Unit',
        'Lokasi',
        'Detail Lokasi',
        'Control Room',
        'Aktivitas',
        'Catatan',
    ];

    /** @var list<string> */
    private const ORANG_TEMPLATE_HEADERS = [
        'Tanggal Plan',
        'Shift',
        'Status',
        'SID',
        'Nama',
        'NIK',
        'Perusahaan',
        'Site',
        'Dept',
        'Lokasi',
        'Detail Lokasi',
        'Control Room',
        'Aktivitas',
        'Catatan',
    ];

    public function __construct(
        private readonly PembatasanLVShiftService $shiftService,
        private readonly PembatasanLVControlRoomContextService $controlRoomContext,
        private readonly PembatasanLVPlanningCheckinService $planningCheckinService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        return view('PembatasanLV.planning.index', [
            'navActive' => 'planning',
            'navItems' => $this->pembatasanLvNavItems(),
            'formContext' => $this->pembatasanLvInputasiFormContext($this->shiftService, $this->controlRoomContext, $user),
            'aktivitasOptions' => $this->pembatasanLvAktivitasOptions(),
        ]);
    }

    public function dataLv(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);
        $page = max((int) $request->query('page', 1), 1);

        $query = PembatasanLvPlanning::query()
            ->whereNull('checked_in_at')
            ->orderByDesc('tanggal_plan')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('no_lambung', 'like', '%'.$q.'%')
                    ->orWhere('nama_driver', 'like', '%'.$q.'%')
                    ->orWhere('lokasi', 'like', '%'.$q.'%')
                    ->orWhere('control_room', 'like', '%'.$q.'%')
                    ->orWhere('aktivitas', 'like', '%'.$q.'%');
            });
        }

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (PembatasanLvPlanning $row) => [
                'id' => $row->id,
                'tanggal_plan' => $row->tanggal_plan?->format('d M Y'),
                'shift' => (int) $row->shift,
                'shift_label' => $this->shiftService->shiftLabel((int) $row->shift),
                'status' => $row->status,
                'nama_driver' => $row->nama_driver,
                'no_lambung' => $row->no_lambung,
                'lokasi' => $row->lokasi,
                'detail_lokasi' => $row->detail_lokasi ?? '',
                'control_room' => $row->control_room,
                'aktivitas' => $row->aktivitas ?? '',
                'creator_name' => $row->creator_name,
            ])->values(),
            'meta' => $this->paginationMeta($paginator),
        ]);
    }

    public function dataOrang(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);
        $page = max((int) $request->query('page', 1), 1);

        $query = PembatasanOrangPlanning::query()
            ->whereNull('checked_in_at')
            ->orderByDesc('tanggal_plan')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('sid', 'like', '%'.$q.'%')
                    ->orWhere('nama', 'like', '%'.$q.'%')
                    ->orWhere('lokasi', 'like', '%'.$q.'%')
                    ->orWhere('control_room', 'like', '%'.$q.'%')
                    ->orWhere('aktivitas', 'like', '%'.$q.'%');
            });
        }

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (PembatasanOrangPlanning $row) => [
                'id' => $row->id,
                'tanggal_plan' => $row->tanggal_plan?->format('d M Y'),
                'shift' => (int) $row->shift,
                'shift_label' => $this->shiftService->shiftLabel((int) $row->shift),
                'status' => $row->status,
                'sid' => $row->sid,
                'nama' => $row->nama,
                'lokasi' => $row->lokasi,
                'detail_lokasi' => $row->detail_lokasi ?? '',
                'control_room' => $row->control_room,
                'aktivitas' => $row->aktivitas ?? '',
                'creator_name' => $row->creator_name,
            ])->values(),
            'meta' => $this->paginationMeta($paginator),
        ]);
    }

    public function storeLv(PembatasanLVPlanningLvRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $controlRoom = trim((string) $request->validated('control_room'));

        if ($controlRoom === '') {
            return redirect()
                ->route('pembatasan-lv.planning.index', ['tab' => 'lv', 'open_planning' => 'lv'])
                ->withInput()
                ->withErrors(['control_room' => 'Control room tidak ditemukan untuk akun Anda.']);
        }

        PembatasanLvPlanning::query()->create([
            ...$request->validated(),
            'creator_id' => $user?->id,
            'creator_name' => (string) ($user?->name ?? '—'),
        ]);

        return redirect()
            ->route('pembatasan-lv.planning.index', ['tab' => 'lv'])
            ->with('success', 'Planning LV berhasil disimpan.');
    }

    public function storeOrang(PembatasanLVPlanningOrangRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $controlRoom = trim((string) $request->validated('control_room'));

        if ($controlRoom === '') {
            return redirect()
                ->route('pembatasan-lv.planning.index', ['tab' => 'orang', 'open_planning' => 'orang'])
                ->withInput()
                ->withErrors(['control_room' => 'Control room tidak ditemukan untuk akun Anda.']);
        }

        PembatasanOrangPlanning::query()->create([
            ...$request->validated(),
            'creator_id' => $user?->id,
            'creator_name' => (string) ($user?->name ?? '—'),
        ]);

        return redirect()
            ->route('pembatasan-lv.planning.index', ['tab' => 'orang'])
            ->with('success', 'Planning orang berhasil disimpan.');
    }

    public function destroyLv(PembatasanLvPlanning $lvPlanning): JsonResponse
    {
        $lvPlanning->delete();

        return response()->json([
            'success' => true,
            'message' => 'Planning LV berhasil dihapus.',
        ]);
    }

    public function destroyOrang(PembatasanOrangPlanning $orangPlanning): JsonResponse
    {
        $orangPlanning->delete();

        return response()->json([
            'success' => true,
            'message' => 'Planning orang berhasil dihapus.',
        ]);
    }

    public function pendingOverview(Request $request): JsonResponse
    {
        $filters = [
            'site' => trim((string) $request->query('site', '')),
            'tanggal' => $request->query('tanggal', now()->toDateString()),
            'control_room' => trim((string) $request->query('control_room', '')),
        ];

        $pending = $this->planningCheckinService->pendingOverview(Auth::user(), $filters);

        return response()->json([
            'data' => [
                'lv' => $pending['lv']->map(fn (PembatasanLvPlanning $row) => [
                    'id' => $row->id,
                    'tanggal_plan' => $row->tanggal_plan?->format('d M Y'),
                    'shift_label' => $this->shiftService->shiftLabel((int) $row->shift),
                    'status' => $row->status,
                    'nama_driver' => $row->nama_driver,
                    'no_lambung' => $row->no_lambung,
                    'lokasi' => $row->lokasi,
                    'detail_lokasi' => $row->detail_lokasi ?? '',
                    'control_room' => $row->control_room,
                    'aktivitas' => $row->aktivitas ?? '',
                ])->values(),
                'orang' => $pending['orang']->map(fn (PembatasanOrangPlanning $row) => [
                    'id' => $row->id,
                    'tanggal_plan' => $row->tanggal_plan?->format('d M Y'),
                    'shift_label' => $this->shiftService->shiftLabel((int) $row->shift),
                    'status' => $row->status,
                    'sid' => $row->sid,
                    'nama' => $row->nama,
                    'lokasi' => $row->lokasi,
                    'detail_lokasi' => $row->detail_lokasi ?? '',
                    'control_room' => $row->control_room,
                    'aktivitas' => $row->aktivitas ?? '',
                ])->values(),
            ],
            'meta' => [
                'tanggal' => $filters['tanggal'],
                'total_lv' => $pending['lv']->count(),
                'total_orang' => $pending['orang']->count(),
            ],
        ]);
    }

    public function checkinLv(PembatasanLvPlanning $lvPlanning): JsonResponse
    {
        try {
            $inputasi = $this->planningCheckinService->checkinLv($lvPlanning, Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Check-in LV '.$inputasi->no_lambung.' berhasil. Unit masuk area.',
                'data' => [
                    'inputasi_id' => $inputasi->id,
                    'no_lambung' => $inputasi->no_lambung,
                ],
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function checkinOrang(PembatasanOrangPlanning $orangPlanning): JsonResponse
    {
        try {
            $inputasi = $this->planningCheckinService->checkinOrang($orangPlanning, Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Check-in '.$inputasi->nama.' (SID: '.$inputasi->sid.') berhasil. Personel masuk area.',
                'data' => [
                    'inputasi_id' => $inputasi->id,
                    'sid' => $inputasi->sid,
                ],
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function downloadTemplateLv(): StreamedResponse
    {
        return $this->streamTemplate(
            'Template Planning LV',
            self::LV_TEMPLATE_HEADERS,
            [
                [now()->addDay()->format('Y-m-d'), 1, 'schedule', 'Budi Santoso', 'DRV001', 'LV-101', 'U001', 'Pit A', 'Area CS', 'CR-01', 'Mobilisasi material', 'Contoh baris'],
            ],
            'pembatasan_lv_planning_lv_template_'.date('Y-m-d').'.xlsx'
        );
    }

    public function downloadTemplateOrang(): StreamedResponse
    {
        return $this->streamTemplate(
            'Template Planning Orang',
            self::ORANG_TEMPLATE_HEADERS,
            [
                [now()->addDay()->format('Y-m-d'), 1, 'schedule', 'SID12345', 'Andi Wijaya', '3201010101010001', 'PT ABC', 'GMO', 'HRGS', 'Pit A', 'Area CS', 'CR-01', 'Inspeksi area', 'Contoh baris'],
            ],
            'pembatasan_lv_planning_orang_template_'.date('Y-m-d').'.xlsx'
        );
    }

    public function importLv(Request $request): RedirectResponse
    {
        return $this->importExcel(
            $request,
            'lv',
            function (array $row, int $rowNum, array &$errors): ?array {
                $tanggal = $this->parseDate($row[0] ?? null);
                $shift = $this->parseShift($row[1] ?? null);
                $status = $this->cellString($row[2] ?? null);
                $namaDriver = $this->cellString($row[3] ?? null);
                $driverRef = $this->cellString($row[4] ?? null);
                $noLambung = $this->cellString($row[5] ?? null);
                $idUnit = $this->cellString($row[6] ?? null);
                $lokasi = $this->cellString($row[7] ?? null);
                $detailLokasi = $this->cellString($row[8] ?? null);
                $controlRoom = $this->cellString($row[9] ?? null);
                $aktivitas = $this->cellString($row[10] ?? null);
                $catatan = $this->cellString($row[11] ?? null);

                if ($tanggal === null) {
                    $errors[] = "Baris {$rowNum}: Tanggal Plan tidak valid.";

                    return null;
                }

                if ($shift === null) {
                    $errors[] = "Baris {$rowNum}: Shift harus 1 atau 2.";

                    return null;
                }

                if (! in_array($status, ['schedule', 'unschedule'], true)) {
                    $errors[] = "Baris {$rowNum}: Status harus schedule atau unschedule.";

                    return null;
                }

                if ($namaDriver === '' || $noLambung === '' || $lokasi === '' || $controlRoom === '') {
                    $errors[] = "Baris {$rowNum}: Nama Driver, No Unit, Lokasi, dan Control Room wajib diisi.";

                    return null;
                }

                $user = Auth::user();

                return [
                    'tanggal_plan' => $tanggal,
                    'shift' => $shift,
                    'status' => $status,
                    'nama_driver' => $namaDriver,
                    'driver_ref' => $driverRef !== '' ? $driverRef : null,
                    'no_lambung' => $noLambung,
                    'id_unit' => $idUnit !== '' ? $idUnit : null,
                    'lokasi' => $lokasi,
                    'detail_lokasi' => $detailLokasi !== '' ? $detailLokasi : null,
                    'control_room' => $controlRoom,
                    'aktivitas' => $aktivitas !== '' ? $aktivitas : null,
                    'catatan' => $catatan !== '' ? $catatan : null,
                    'creator_id' => $user?->id,
                    'creator_name' => (string) ($user?->name ?? '—'),
                ];
            },
            PembatasanLvPlanning::class
        );
    }

    public function importOrang(Request $request): RedirectResponse
    {
        return $this->importExcel(
            $request,
            'orang',
            function (array $row, int $rowNum, array &$errors): ?array {
                $tanggal = $this->parseDate($row[0] ?? null);
                $shift = $this->parseShift($row[1] ?? null);
                $status = $this->cellString($row[2] ?? null);
                $sid = $this->cellString($row[3] ?? null);
                $nama = $this->cellString($row[4] ?? null);
                $nik = $this->cellString($row[5] ?? null);
                $perusahaan = $this->cellString($row[6] ?? null);
                $site = $this->cellString($row[7] ?? null);
                $dept = $this->cellString($row[8] ?? null);
                $lokasi = $this->cellString($row[9] ?? null);
                $detailLokasi = $this->cellString($row[10] ?? null);
                $controlRoom = $this->cellString($row[11] ?? null);
                $aktivitas = $this->cellString($row[12] ?? null);
                $catatan = $this->cellString($row[13] ?? null);

                if ($tanggal === null) {
                    $errors[] = "Baris {$rowNum}: Tanggal Plan tidak valid.";

                    return null;
                }

                if ($shift === null) {
                    $errors[] = "Baris {$rowNum}: Shift harus 1 atau 2.";

                    return null;
                }

                if (! in_array($status, ['schedule', 'unschedule'], true)) {
                    $errors[] = "Baris {$rowNum}: Status harus schedule atau unschedule.";

                    return null;
                }

                if ($sid === '' || $nama === '' || $lokasi === '' || $controlRoom === '') {
                    $errors[] = "Baris {$rowNum}: SID, Nama, Lokasi, dan Control Room wajib diisi.";

                    return null;
                }

                $user = Auth::user();

                return [
                    'tanggal_plan' => $tanggal,
                    'shift' => $shift,
                    'status' => $status,
                    'sid' => $sid,
                    'nama' => $nama,
                    'nik' => $nik !== '' ? $nik : null,
                    'nama_perusahaan' => $perusahaan !== '' ? $perusahaan : null,
                    'site' => $site !== '' ? $site : null,
                    'dept' => $dept !== '' ? $dept : null,
                    'lokasi' => $lokasi,
                    'detail_lokasi' => $detailLokasi !== '' ? $detailLokasi : null,
                    'control_room' => $controlRoom,
                    'aktivitas' => $aktivitas !== '' ? $aktivitas : null,
                    'catatan' => $catatan !== '' ? $catatan : null,
                    'creator_id' => $user?->id,
                    'creator_name' => (string) ($user?->name ?? '—'),
                ];
            },
            PembatasanOrangPlanning::class
        );
    }

    /**
     * @param  list<string>  $headers
     * @param  list<list<mixed>>  $sampleRows
     */
    private function streamTemplate(string $sheetTitle, array $headers, array $sampleRows, string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetTitle);

        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).'1', $header);
            $col++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($sampleRows as $sampleRow) {
            $col = 1;
            foreach ($sampleRow as $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$rowNum, $value);
                $col++;
            }
            $rowNum++;
        }

        foreach (range(1, count($headers)) as $columnIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer): void {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @param  callable(array<int, mixed>, int, array<int, string>): ?array<string, mixed>  $mapRow
     * @param  class-string  $modelClass
     */
    private function importExcel(Request $request, string $tab, callable $mapRow, string $modelClass): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'excel_file.required' => 'File Excel wajib diupload.',
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $dataRows = array_slice($rows, 1);
            $imported = 0;
            $errors = [];
            $rowNum = 2;

            DB::beginTransaction();

            foreach ($dataRows as $row) {
                if ($this->isEmptyRow($row)) {
                    $rowNum++;

                    continue;
                }

                $payload = $mapRow($row, $rowNum, $errors);
                if ($payload === null) {
                    $rowNum++;

                    continue;
                }

                $modelClass::query()->create($payload);
                $imported++;
                $rowNum++;
            }

            if ($imported === 0 && $errors !== []) {
                DB::rollBack();

                return redirect()
                    ->route('pembatasan-lv.planning.index', ['tab' => $tab, 'modal' => 'import-'.$tab])
                    ->with('error', 'Import gagal. Perbaiki isi file Excel.')
                    ->with('import_errors', array_slice($errors, 0, 20));
            }

            DB::commit();

            $message = "Import planning {$tab} berhasil: {$imported} baris ditambahkan.";
            if ($errors !== []) {
                $message .= ' Peringatan: '.implode(' ', array_slice($errors, 0, 3));
            }

            return redirect()
                ->route('pembatasan-lv.planning.index', ['tab' => $tab])
                ->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PembatasanLVPlanning import {$tab}: ".$e->getMessage());

            return redirect()
                ->route('pembatasan-lv.planning.index', ['tab' => $tab, 'modal' => 'import-'.$tab])
                ->with('error', 'Gagal import: '.$e->getMessage());
        }
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
     * @return array<string, int>
     */
    private function paginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem() ?? 0,
            'to' => $paginator->lastItem() ?? 0,
        ];
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, fn ($cell) => trim((string) ($cell ?? '')) !== ''));
    }

    private function cellString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function parseShift(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $shift = (int) $value;

        return in_array($shift, [1, 2], true) ? $shift : null;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))
                    ->timezone(config('app.timezone'))
                    ->format('Y-m-d');
            } catch (Exception) {
                return null;
            }
        }

        $str = trim((string) $value);
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'd M Y', 'd/m/y'];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $str, config('app.timezone'));

                if ($parsed !== false) {
                    return $parsed->format('Y-m-d');
                }
            } catch (Exception) {
                continue;
            }
        }

        try {
            return Carbon::parse($str, config('app.timezone'))->format('Y-m-d');
        } catch (Exception) {
            return null;
        }
    }
}
