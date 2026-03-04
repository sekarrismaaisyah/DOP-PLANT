<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Models\DailyOperationPlan;
use App\Models\DopPicBerauCoal;
use App\Models\DopPengawasMitraKerja;
use App\Models\CctvData;
use App\Models\MasterAktivitas;
use App\Services\ClickHouseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DOPController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $dops = DailyOperationPlan::with(['picBerauCoal', 'pengawasMitraKerja', 'cctvs'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('SistemRoster.dop.index', compact('dops', 'perPage'));
    }

    public function create(): View
    {
        $cctvs = CctvData::select('id', 'nama_cctv', 'no_cctv', 'lokasi_pemasangan')
            ->orderBy('nama_cctv')
            ->get();

        $masterAktivitas = MasterAktivitas::orderBy('nama_aktivitas')->get();

        $lokasiList = $this->getLokasiFromClickHouse();

        return view('SistemRoster.dop.create', compact('cctvs', 'masterAktivitas', 'lokasiList'));
    }

    /**
     * Ambil daftar lokasi dari ClickHouse: hse_automation.lokasi_detail_lokasi (status = 1).
     */
    private function getLokasiFromClickHouse(): array
    {
        try {
            $clickHouse = app(ClickHouseService::class);
            if (!$clickHouse->isConnected()) {
                Log::info('DOP: ClickHouse not connected, lokasi list empty');
                return [];
            }

            $sql = "
                SELECT
                    toString(site) as site,
                    toString(lokasi) as lokasi,
                    toString(coalesce(detil_lokasi, '')) as detil_lokasi
                FROM hse_automation.lokasi_detail_lokasi
                WHERE status_site = 1 AND status_lokasi = 1 AND status_detil_lokasi = 1
                ORDER BY site, lokasi, detil_lokasi
            ";

            try {
                $rows = $clickHouse->query($sql) ?? [];
            } catch (\Throwable $e) {
                // Fallback jika kolom detail bernama "Detil Lokasi" (dengan spasi)
                $sql = "
                    SELECT
                        toString(site) as site,
                        toString(lokasi) as lokasi,
                        toString(coalesce(`Detil Lokasi`, '')) as detil_lokasi
                    FROM hse_automation.lokasi_detail_lokasi
                    WHERE status_site = 1 AND status_lokasi = 1 AND status_detil_lokasi = 1
                    ORDER BY site, lokasi, detil_lokasi
                ";
                $rows = $clickHouse->query($sql) ?? [];
            }
            if (!empty($rows)) {
                Log::info('DOP: Lokasi loaded from hse_automation.lokasi_detail_lokasi, count: ' . count($rows));
            }
            return $rows;
        } catch (\Throwable $e) {
            Log::warning('DOP: Could not load lokasi from ClickHouse: ' . $e->getMessage());
            return [];
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pekerjaan' => ['required', 'string', 'max:255'],
            'aktivitas' => ['nullable', 'string', 'max:255'],
            'foto_pekerjaan' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'unit_id' => ['nullable', 'string', 'max:255'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'detail_lokasi' => ['nullable', 'string'],
            'cctv_ids' => ['nullable', 'array'],
            'cctv_ids.*' => ['exists:cctv_data_bmo2,id'],
            'potensi_resiko' => ['nullable', 'string'],
            'pengendalian_bahaya' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
            'pic_berau_coal' => ['nullable', 'array'],
            'pic_berau_coal.*.shift' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            'pic_berau_coal.*.nama_pic' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            'pengawas_mitra_kerja' => ['nullable', 'array'],
            'pengawas_mitra_kerja.*.shift' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
            'pengawas_mitra_kerja.*.nama_pengawas' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_pekerjaan')) {
            $fotoFile = $request->file('foto_pekerjaan');
            $fotoPath = $fotoFile->store('dop/foto-pekerjaan', 'public');
        }

        $dop = DailyOperationPlan::create([
            'pekerjaan' => $validated['pekerjaan'],
            'aktivitas' => $validated['aktivitas'] ?? null,
            'foto_pekerjaan' => $fotoPath,
            'unit_id' => $validated['unit_id'],
            'perusahaan' => $validated['perusahaan'] ?? null,
            'lokasi' => $validated['lokasi'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'detail_lokasi' => $validated['detail_lokasi'] ?? null,
            'potensi_resiko' => $validated['potensi_resiko'] ?? null,
            'pengendalian_bahaya' => $validated['pengendalian_bahaya'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'tanggal' => $validated['tanggal'],
        ]);

        if (!empty($validated['cctv_ids'])) {
            $dop->cctvs()->sync($validated['cctv_ids']);
        }

        if (!empty($validated['pic_berau_coal'])) {
            foreach ($validated['pic_berau_coal'] as $picData) {
                if (!empty($picData['nama_pic'])) {
                    DopPicBerauCoal::create([
                        'dop_id' => $dop->id,
                        'shift' => $picData['shift'],
                        'nama_pic' => $picData['nama_pic'],
                        'layer' => $picData['layer'] ?? null,
                    ]);
                }
            }
        }

        if (!empty($validated['pengawas_mitra_kerja'])) {
            foreach ($validated['pengawas_mitra_kerja'] as $pengawasData) {
                if (!empty($pengawasData['nama_pengawas'])) {
                    DopPengawasMitraKerja::create([
                        'dop_id' => $dop->id,
                        'shift' => $pengawasData['shift'],
                        'nama_pengawas' => $pengawasData['nama_pengawas'],
                        'layer' => $pengawasData['layer'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('sistem-roster.dop.index')
            ->with('success', 'DOP berhasil disimpan.');
    }

    public function show($id): View
    {
        $dop = DailyOperationPlan::with(['picBerauCoal', 'pengawasMitraKerja', 'cctvs'])->findOrFail($id);
        return view('SistemRoster.dop.show', compact('dop'));
    }

    public function edit($id): View
    {
        $dop = DailyOperationPlan::with(['picBerauCoal', 'pengawasMitraKerja', 'cctvs'])->findOrFail($id);
        $cctvs = CctvData::select('id', 'nama_cctv', 'no_cctv', 'lokasi_pemasangan')
            ->orderBy('nama_cctv')
            ->get();
        $masterAktivitas = MasterAktivitas::orderBy('nama_aktivitas')->get();
        $lokasiList = $this->getLokasiFromClickHouse();

        return view('SistemRoster.dop.edit', compact('dop', 'cctvs', 'masterAktivitas', 'lokasiList'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $dop = DailyOperationPlan::findOrFail($id);

        $validated = $request->validate([
            'pekerjaan' => ['required', 'string', 'max:255'],
            'aktivitas' => ['nullable', 'string', 'max:255'],
            'foto_pekerjaan' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'unit_id' => ['required', 'string', 'max:255'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'detail_lokasi' => ['nullable', 'string'],
            'cctv_ids' => ['nullable', 'array'],
            'cctv_ids.*' => ['exists:cctv_data_bmo2,id'],
            'potensi_resiko' => ['nullable', 'string'],
            'pengendalian_bahaya' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
            'pic_berau_coal' => ['nullable', 'array'],
            'pic_berau_coal.*.shift' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            'pic_berau_coal.*.nama_pic' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            'pengawas_mitra_kerja' => ['nullable', 'array'],
            'pengawas_mitra_kerja.*.shift' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
            'pengawas_mitra_kerja.*.nama_pengawas' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
        ]);

        if ($request->hasFile('foto_pekerjaan')) {
            if ($dop->foto_pekerjaan) {
                Storage::disk('public')->delete($dop->foto_pekerjaan);
            }
            
            $fotoFile = $request->file('foto_pekerjaan');
            $fotoPath = $fotoFile->store('dop/foto-pekerjaan', 'public');
            $validated['foto_pekerjaan'] = $fotoPath;
        }

        $dop->fill($validated);
        $dop->save();

        if (isset($validated['cctv_ids'])) {
            $dop->cctvs()->sync($validated['cctv_ids']);
        } else {
            $dop->cctvs()->sync([]);
        }

        $dop->picBerauCoal()->delete();
        $dop->pengawasMitraKerja()->delete();

        if (!empty($validated['pic_berau_coal'])) {
            foreach ($validated['pic_berau_coal'] as $picData) {
                if (!empty($picData['nama_pic'])) {
                    DopPicBerauCoal::create([
                        'dop_id' => $dop->id,
                        'shift' => $picData['shift'],
                        'nama_pic' => $picData['nama_pic'],
                        'layer' => $picData['layer'] ?? null,
                    ]);
                }
            }
        }

        if (!empty($validated['pengawas_mitra_kerja'])) {
            foreach ($validated['pengawas_mitra_kerja'] as $pengawasData) {
                if (!empty($pengawasData['nama_pengawas'])) {
                    DopPengawasMitraKerja::create([
                        'dop_id' => $dop->id,
                        'shift' => $pengawasData['shift'],
                        'nama_pengawas' => $pengawasData['nama_pengawas'],
                        'layer' => $pengawasData['layer'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('sistem-roster.dop.index')
            ->with('success', 'DOP berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $dop = DailyOperationPlan::findOrFail($id);

        if ($dop->foto_pekerjaan) {
            Storage::disk('public')->delete($dop->foto_pekerjaan);
        }

        $dop->picBerauCoal()->delete();
        $dop->pengawasMitraKerja()->delete();
        $dop->delete();

        return redirect()
            ->route('sistem-roster.dop.index')
            ->with('success', 'DOP berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $dop = DailyOperationPlan::findOrFail($id);
        $dop->status = !$dop->status;
        $dop->save();

        return response()->json([
            'success' => true,
            'status' => $dop->status,
            'message' => $dop->status ? 'DOP diaktifkan' : 'DOP dinonaktifkan',
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = [
            'A1' => 'Tanggal',
            'B1' => 'Pekerjaan',
            'C1' => 'Unit ID',
            'D1' => 'Lokasi',
            'E1' => 'Latitude',
            'F1' => 'Longitude',
            'G1' => 'Detail Lokasi',
            'H1' => 'Potensi Risiko',
            'I1' => 'Pengendalian Bahaya',
            'J1' => 'Catatan',
            'K1' => 'CCTV IDs (pisahkan dengan koma)',
            'L1' => 'PIC Berau Coal - Shift',
            'M1' => 'PIC Berau Coal - Nama PIC',
            'N1' => 'PIC Berau Coal - Layer',
            'O1' => 'Pengawas Mitra Kerja - Shift',
            'P1' => 'Pengawas Mitra Kerja - Nama Pengawas',
            'Q1' => 'Pengawas Mitra Kerja - Layer',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(25);
        $sheet->getColumnDimension('N')->setWidth(15);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(25);
        $sheet->getColumnDimension('Q')->setWidth(15);

        $exampleRow = 2;
        $sheet->setCellValue('A' . $exampleRow, '2026-01-15');
        $sheet->setCellValue('B' . $exampleRow, 'Pemeliharaan Unit Excavator');
        $sheet->setCellValue('C' . $exampleRow, 'EX-001');
        $sheet->setCellValue('D' . $exampleRow, 'Area Tambang Utara');
        $sheet->setCellValue('E' . $exampleRow, '-2.186253');
        $sheet->setCellValue('F' . $exampleRow, '117.4539035');
        $sheet->setCellValue('G' . $exampleRow, 'Koordinat: -2.186253, 117.4539035');
        $sheet->setCellValue('H' . $exampleRow, 'Tenggelam, Terbalik');
        $sheet->setCellValue('I' . $exampleRow, 'Assessment, JSA, SOP');
        $sheet->setCellValue('J' . $exampleRow, 'Pekerjaan dilakukan pada shift pagi');
        $sheet->setCellValue('K' . $exampleRow, '1,2,3');
        $sheet->setCellValue('L' . $exampleRow, 'Shift 1 s/d 2');
        $sheet->setCellValue('M' . $exampleRow, 'John Doe');
        $sheet->setCellValue('N' . $exampleRow, 'Layer 1');
        $sheet->setCellValue('O' . $exampleRow, 'Shift 1 s/d 2');
        $sheet->setCellValue('P' . $exampleRow, 'Jane Smith');
        $sheet->setCellValue('Q' . $exampleRow, 'Layer 2');

        $noteRow = 4;
        $sheet->setCellValue('A' . $noteRow, 'CATATAN:');
        $sheet->getStyle('A' . $noteRow)->getFont()->setBold(true);
        
        $notes = [
            'A5' => '1. Tanggal: Format YYYY-MM-DD (contoh: 2026-01-15)',
            'A6' => '2. CCTV IDs: Pisahkan dengan koma jika lebih dari satu (contoh: 1,2,3)',
            'A7' => '3. Shift: Gunakan "Shift 1 s/d 2" atau "Shift 2 s/d 1"',
            'A8' => '4. Potensi Risiko: Pisahkan dengan koma jika lebih dari satu',
            'A9' => '5. Pengendalian Bahaya: Pisahkan dengan koma jika lebih dari satu',
            'A10' => '6. Untuk PIC/Pengawas multiple, buat baris baru dengan data yang sama kecuali PIC/Pengawas',
        ];

        foreach ($notes as $cell => $note) {
            $sheet->setCellValue($cell, $note);
            $sheet->getStyle($cell)->getFont()->setItalic(true);
            $sheet->getStyle($cell)->getFont()->getColor()->setRGB('666666');
        }

        $sheet->freezePane('A2');

        $filename = 'template_dop_sistem_roster_' . date('Y-m-d') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $dataRows = array_slice($rows, 1);
            
            $imported = 0;
            $errors = [];
            $rowNumber = 2;
            $dopCache = [];

            DB::beginTransaction();

            foreach ($dataRows as $row) {
                if (empty(array_filter($row))) {
                    $rowNumber++;
                    continue;
                }

                try {
                    if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3])) {
                        $errors[] = "Baris {$rowNumber}: Tanggal, Pekerjaan, Unit ID, dan Lokasi wajib diisi";
                        $rowNumber++;
                        continue;
                    }

                    $tanggal = $row[0];
                    if (is_numeric($tanggal)) {
                        $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
                    } else {
                        $parsedDate = date_create($tanggal);
                        if (!$parsedDate) {
                            throw new \Exception("Format tanggal tidak valid: {$tanggal}");
                        }
                        $tanggal = $parsedDate->format('Y-m-d');
                    }

                    $dopKey = md5($tanggal . '|' . ($row[1] ?? '') . '|' . ($row[2] ?? '') . '|' . ($row[3] ?? ''));

                    if (isset($dopCache[$dopKey])) {
                        $dop = $dopCache[$dopKey];
                    } else {
                        $dop = DailyOperationPlan::where('tanggal', $tanggal)
                            ->where('pekerjaan', $row[1] ?? '')
                            ->where('unit_id', $row[2] ?? '')
                            ->where('lokasi', $row[3] ?? '')
                            ->first();

                        if (!$dop) {
                            $latitude = !empty($row[4]) ? (is_numeric($row[4]) ? (float)$row[4] : null) : null;
                            $longitude = !empty($row[5]) ? (is_numeric($row[5]) ? (float)$row[5] : null) : null;
                            
                            if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
                                $latitude = null;
                            }
                            if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
                                $longitude = null;
                            }
                            
                            $dop = DailyOperationPlan::create([
                                'tanggal' => $tanggal,
                                'pekerjaan' => $row[1] ?? '',
                                'unit_id' => $row[2] ?? '',
                                'lokasi' => $row[3] ?? '',
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'detail_lokasi' => $row[6] ?? null,
                                'potensi_resiko' => $row[7] ?? null,
                                'pengendalian_bahaya' => $row[8] ?? null,
                                'catatan' => $row[9] ?? null,
                                'foto_pekerjaan' => null,
                            ]);
                            $imported++;
                        }

                        $dopCache[$dopKey] = $dop;

                        if (!empty($row[10])) {
                            $cctvIds = array_map('trim', explode(',', $row[10]));
                            $cctvIds = array_filter($cctvIds, function($id) {
                                return is_numeric($id) && CctvData::where('id', $id)->exists();
                            });
                            if (!empty($cctvIds)) {
                                $existingIds = $dop->cctvs()->pluck('id')->toArray();
                                $allIds = array_unique(array_merge($existingIds, $cctvIds));
                                $dop->cctvs()->sync($allIds);
                            }
                        }
                    }

                    if (!empty($row[11]) && !empty($row[12])) {
                        $picExists = DopPicBerauCoal::where('dop_id', $dop->id)
                            ->where('shift', $row[11] ?? null)
                            ->where('nama_pic', $row[12] ?? '')
                            ->exists();
                        
                        if (!$picExists) {
                            DopPicBerauCoal::create([
                                'dop_id' => $dop->id,
                                'shift' => $row[11] ?? null,
                                'nama_pic' => $row[12] ?? '',
                                'layer' => $row[13] ?? null,
                            ]);
                        }
                    }

                    if (!empty($row[14]) && !empty($row[15])) {
                        $pengawasExists = DopPengawasMitraKerja::where('dop_id', $dop->id)
                            ->where('shift', $row[14] ?? null)
                            ->where('nama_pengawas', $row[15] ?? '')
                            ->exists();
                        
                        if (!$pengawasExists) {
                            DopPengawasMitraKerja::create([
                                'dop_id' => $dop->id,
                                'shift' => $row[14] ?? null,
                                'nama_pengawas' => $row[15] ?? '',
                                'layer' => $row[16] ?? null,
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    Log::error("DOP Import Error - Row {$rowNumber}: " . $e->getMessage());
                }

                $rowNumber++;
            }

            DB::commit();

            $message = "Berhasil mengimpor {$imported} data DOP.";
            if (!empty($errors)) {
                $message .= " Terjadi " . count($errors) . " error. " . implode(' | ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (dan " . (count($errors) - 5) . " error lainnya)";
                }
            }

            return redirect()
                ->route('sistem-roster.dop.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DOP Import Error: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengimpor file Excel: ' . $e->getMessage());
        }
    }
}
