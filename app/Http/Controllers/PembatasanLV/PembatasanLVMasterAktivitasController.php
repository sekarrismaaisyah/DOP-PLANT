<?php

declare(strict_types=1);

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Requests\PembatasanLV\PembatasanLVMasterAktivitasRequest;
use App\Models\PembatasanMasterAktivitas;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PembatasanLVMasterAktivitasController extends Controller
{
    /** @var list<string> */
    private const TEMPLATE_HEADERS = [
        'Site',
        'Perusahaan',
        'Departemen',
        'Kategori Aktivitas Pekerjaan di Luar Kabin',
        'Detail Aktivitas Pengoperasian LV',
        'Frekuensi Aktivitas dalam 1 shift',
        'Estimasi Jumlah LV beraktivitas dalam 1 shift',
    ];

    public function data(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);
        $page = max((int) $request->query('page', 1), 1);

        $query = PembatasanMasterAktivitas::query()
            ->orderBy('site')
            ->orderBy('perusahaan')
            ->orderBy('departemen');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('site', 'like', '%'.$q.'%')
                    ->orWhere('perusahaan', 'like', '%'.$q.'%')
                    ->orWhere('departemen', 'like', '%'.$q.'%')
                    ->orWhere('kategori_aktivitas_luar_kabin', 'like', '%'.$q.'%')
                    ->orWhere('detail_aktivitas_pengoperasian_lv', 'like', '%'.$q.'%');
            });
        }

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (PembatasanMasterAktivitas $row) => [
                'id' => $row->id,
                'site' => $row->site,
                'perusahaan' => $row->perusahaan,
                'departemen' => $row->departemen,
                'kategori_aktivitas_luar_kabin' => $row->kategori_aktivitas_luar_kabin,
                'detail_aktivitas_pengoperasian_lv' => $row->detail_aktivitas_pengoperasian_lv,
                'frekuensi_aktivitas_per_shift' => (int) $row->frekuensi_aktivitas_per_shift,
                'estimasi_jumlah_lv_per_shift' => (int) $row->estimasi_jumlah_lv_per_shift,
                'updated_at' => $row->updated_at?->format('d M Y H:i'),
            ])->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem() ?? 0,
                'to' => $paginator->lastItem() ?? 0,
            ],
        ]);
    }

    public function show(PembatasanMasterAktivitas $masterAktivitas): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $masterAktivitas->id,
                'site' => $masterAktivitas->site,
                'perusahaan' => $masterAktivitas->perusahaan,
                'departemen' => $masterAktivitas->departemen,
                'kategori_aktivitas_luar_kabin' => $masterAktivitas->kategori_aktivitas_luar_kabin,
                'detail_aktivitas_pengoperasian_lv' => $masterAktivitas->detail_aktivitas_pengoperasian_lv,
                'frekuensi_aktivitas_per_shift' => (int) $masterAktivitas->frekuensi_aktivitas_per_shift,
                'estimasi_jumlah_lv_per_shift' => (int) $masterAktivitas->estimasi_jumlah_lv_per_shift,
            ],
        ]);
    }

    public function store(PembatasanLVMasterAktivitasRequest $request): JsonResponse
    {
        $row = PembatasanMasterAktivitas::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data aktivitas berhasil ditambahkan.',
            'data' => $row,
        ], 201);
    }

    public function update(PembatasanLVMasterAktivitasRequest $request, PembatasanMasterAktivitas $masterAktivitas): JsonResponse
    {
        $masterAktivitas->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data aktivitas berhasil diperbarui.',
            'data' => $masterAktivitas->fresh(),
        ]);
    }

    public function destroy(PembatasanMasterAktivitas $masterAktivitas): JsonResponse
    {
        $masterAktivitas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data aktivitas berhasil dihapus.',
        ]);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Aktivitas');

        $col = 1;
        foreach (self::TEMPLATE_HEADERS as $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).'1', $header);
            $col++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count(self::TEMPLATE_HEADERS));
        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->setBold(true);

        $sampleRows = [
            ['LMO', 'BUMA', 'HRGS', 'Catering', 'Pengantaran Packmeal', 1, 2],
            ['GMO', 'MOP', 'Produksi & Plant', 'Mobilisasi karyawan', 'Penjemputan dan Pengantaran Karyawan di area CS', 4, 4],
            ['GMO', 'ACI', 'Dewatering', 'Pekerjaan Dewatering dan Pekerjaan Pipa', 'Mobilisasi Man Power dan Peralatan ke area kerja', 2, 2],
            [
                'BMO 1',
                'PT HRB',
                'ENVIRO',
                "1) Mobilisasi material dan karyawan\n2) Escort transporter material",
                "1) Bongkar dan muat material ke lokasi Revegetasi\n2) Pengantaran dan penjemputan karyawan\n3) Escort transporter muatan material penanaman",
                3,
                1,
            ],
            [
                'BMO 2',
                'PT HRB',
                'ENVIRO',
                "1) Mobilisasi material dan karyawan\n2) Escort transporter material",
                "1) Bongkar dan muat material ke lokasi Revegetasi\n2) Pengantaran dan penjemputan karyawan\n3) Escort transporter muatan material penanaman",
                3,
                3,
            ],
            [
                'GMO',
                'PT HRB',
                'ENVIRO',
                "1) Mobilisasi material dan karyawan\n2) Escort transporter material",
                "1) Bongkar dan muat material ke lokasi Revegetasi\n2) Pengantaran dan penjemputan karyawan\n3) Escort transporter muatan material penanaman",
                3,
                2,
            ],
            [
                'LMO',
                'PT HRB',
                'ENVIRO',
                "1) Mobilisasi material dan karyawan\n2) Escort transporter material",
                "1) Bongkar dan muat material ke lokasi Revegetasi\n2) Pengantaran dan penjemputan karyawan\n3) Escort transporter muatan material penanaman",
                3,
                2,
            ],
            [
                'SMO',
                'PT HRB',
                'ENVIRO',
                "1) Mobilisasi material dan karyawan\n2) Escort transporter material",
                "1) Bongkar dan muat material ke lokasi Revegetasi\n2) Pengantaran dan penjemputan karyawan\n3) Escort transporter muatan material penanaman",
                3,
                2,
            ],
            ['BMO 1', 'PT Bukit Makmur Mandiri Utama', 'Produksi', 'Mobilisasi karyawan', 'Penjemputan dan Pengantaran Karyawan', 2, 2],
        ];

        $rowNum = 2;
        foreach ($sampleRows as $sampleRow) {
            $col = 1;
            foreach ($sampleRow as $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$rowNum, $value);
                $col++;
            }
            $rowNum++;
        }

        foreach (range(1, count(self::TEMPLATE_HEADERS)) as $columnIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }

        $filename = 'pembatasan_lv_master_aktivitas_template_'.date('Y-m-d').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer): void {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request): RedirectResponse
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

                $site = $this->cellString($row[0] ?? null);
                $perusahaan = $this->cellString($row[1] ?? null);
                $departemen = $this->cellString($row[2] ?? null);
                $kategori = $this->cellString($row[3] ?? null);
                $detail = $this->cellString($row[4] ?? null);
                $frekuensi = $this->cellInteger($row[5] ?? null);
                $estimasiLv = $this->cellInteger($row[6] ?? null);

                if ($site === '' || $perusahaan === '' || $departemen === '' || $kategori === '' || $detail === '') {
                    $errors[] = "Baris {$rowNum}: Site, Perusahaan, Departemen, Kategori, dan Detail wajib diisi.";
                    $rowNum++;

                    continue;
                }

                if ($frekuensi === null || $estimasiLv === null) {
                    $errors[] = "Baris {$rowNum}: Frekuensi dan Estimasi Jumlah LV harus berupa angka.";
                    $rowNum++;

                    continue;
                }

                PembatasanMasterAktivitas::query()->create([
                    'site' => $site,
                    'perusahaan' => $perusahaan,
                    'departemen' => $departemen,
                    'kategori_aktivitas_luar_kabin' => $kategori,
                    'detail_aktivitas_pengoperasian_lv' => $detail,
                    'frekuensi_aktivitas_per_shift' => $frekuensi,
                    'estimasi_jumlah_lv_per_shift' => $estimasiLv,
                ]);

                $imported++;
                $rowNum++;
            }

            if ($imported === 0 && $errors !== []) {
                DB::rollBack();

                return redirect()
                    ->route('pembatasan-lv.master-data.index', ['tab' => 'aktivitas', 'modal' => 'import'])
                    ->with('error', 'Import gagal. Perbaiki isi file Excel.')
                    ->with('import_errors', array_slice($errors, 0, 20));
            }

            DB::commit();

            $message = "Import berhasil: {$imported} baris ditambahkan.";
            if ($errors !== []) {
                $message .= ' Peringatan: '.implode(' ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= ' ...';
                }
            }

            return redirect()
                ->route('pembatasan-lv.master-data.index', ['tab' => 'aktivitas'])
                ->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('PembatasanLVMasterAktivitas import: '.$e->getMessage());

            return redirect()
                ->route('pembatasan-lv.master-data.index', ['tab' => 'aktivitas', 'modal' => 'import'])
                ->with('error', 'Gagal import: '.$e->getMessage());
        }
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

    private function cellInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $str = trim((string) $value);
        $str = str_replace(',', '.', $str);

        return is_numeric($str) ? (int) $str : null;
    }
}
