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
            'C1' => 'Aktivitas',
            'D1' => 'Site',
            'E1' => 'Unit ID',
            'F1' => 'Lokasi',
            'G1' => 'Latitude',
            'H1' => 'Longitude',
            'I1' => 'Detail Lokasi',
            'J1' => 'Potensi Risiko',
            'K1' => 'Pengendalian Bahaya',
            'L1' => 'Catatan',
            'M1' => 'CCTV IDs (pisahkan dengan koma)',
            'N1' => 'PIC Berau Coal - Shift',
            'O1' => 'PIC Berau Coal - Nama PIC',
            'P1' => 'PIC Berau Coal - Layer',
            'Q1' => 'Pengawas Mitra Kerja - Shift',
            'R1' => 'Pengawas Mitra Kerja - Nama Pengawas',
            'S1' => 'Pengawas Mitra Kerja - Layer',
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
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(30);
        $sheet->getColumnDimension('M')->setWidth(25);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(25);
        $sheet->getColumnDimension('P')->setWidth(15);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(25);
        $sheet->getColumnDimension('S')->setWidth(15);

        $exampleRow = 2;
        $sheet->setCellValue('A' . $exampleRow, '2026-01-15');
        $sheet->setCellValue('B' . $exampleRow, 'Pemeliharaan Unit Excavator');
        $sheet->setCellValue('C' . $exampleRow, 'Pemeliharaan');
        $sheet->setCellValue('D' . $exampleRow, 'BMO 2');
        $sheet->setCellValue('E' . $exampleRow, 'EX-001');
        $sheet->setCellValue('F' . $exampleRow, '(B8) Area Kritis Blok 8');
        $sheet->setCellValue('G' . $exampleRow, '-2.186253');
        $sheet->setCellValue('H' . $exampleRow, '117.4539035');
        $sheet->setCellValue('I' . $exampleRow, '(B8) Running Dragflow WMP 47');
        $sheet->setCellValue('J' . $exampleRow, 'Tenggelam, Terbalik');
        $sheet->setCellValue('K' . $exampleRow, 'Assessment, JSA, SOP');
        $sheet->setCellValue('L' . $exampleRow, 'Pekerjaan dilakukan pada shift pagi');
        $sheet->setCellValue('M' . $exampleRow, '1,2,3');
        $sheet->setCellValue('N' . $exampleRow, 'Shift 1 s/d 2');
        $sheet->setCellValue('O' . $exampleRow, 'John Doe');
        $sheet->setCellValue('P' . $exampleRow, 'Layer 1');
        $sheet->setCellValue('Q' . $exampleRow, 'Shift 1 s/d 2');
        $sheet->setCellValue('R' . $exampleRow, 'Jane Smith');
        $sheet->setCellValue('S' . $exampleRow, 'Layer 2');

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
                    // Template baru: A=Tanggal, B=Pekerjaan, C=Aktivitas, D=Site, E=Unit ID, F=Lokasi, ...
                    $isNewTemplate = isset($row[4]) && isset($row[5]) && count($row) >= 19;
                    $idxTanggal = 0;
                    $idxPekerjaan = 1;
                    $idxAktivitas = $isNewTemplate ? 2 : null;
                    $idxSite = $isNewTemplate ? 3 : null;
                    $idxUnitId = $isNewTemplate ? 4 : 2;
                    $idxLokasi = $isNewTemplate ? 5 : 3;
                    $idxLat = $isNewTemplate ? 6 : 4;
                    $idxLong = $isNewTemplate ? 7 : 5;
                    $idxDetailLokasi = $isNewTemplate ? 8 : 6;
                    $idxPotensi = $isNewTemplate ? 9 : 7;
                    $idxPengendalian = $isNewTemplate ? 10 : 8;
                    $idxCatatan = $isNewTemplate ? 11 : 9;
                    $idxCctv = $isNewTemplate ? 12 : 10;
                    $idxPicShift = $isNewTemplate ? 13 : 11;
                    $idxPicNama = $isNewTemplate ? 14 : 12;
                    $idxPicLayer = $isNewTemplate ? 15 : 13;
                    $idxPengawasShift = $isNewTemplate ? 16 : 14;
                    $idxPengawasNama = $isNewTemplate ? 17 : 15;
                    $idxPengawasLayer = $isNewTemplate ? 18 : 16;

                    if (empty($row[$idxTanggal]) || empty($row[$idxPekerjaan]) || empty($row[$idxUnitId]) || empty($row[$idxLokasi])) {
                        $errors[] = "Baris {$rowNumber}: Tanggal, Pekerjaan, Unit ID, dan Lokasi wajib diisi";
                        $rowNumber++;
                        continue;
                    }

                    $tanggal = $row[$idxTanggal];
                    if (is_numeric($tanggal)) {
                        $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
                    } else {
                        $parsedDate = date_create($tanggal);
                        if (!$parsedDate) {
                            throw new \Exception("Format tanggal tidak valid: {$tanggal}");
                        }
                        $tanggal = $parsedDate->format('Y-m-d');
                    }

                    $dopKey = md5($tanggal . '|' . ($row[$idxPekerjaan] ?? '') . '|' . ($row[$idxUnitId] ?? '') . '|' . ($row[$idxLokasi] ?? ''));

                    if (isset($dopCache[$dopKey])) {
                        $dop = $dopCache[$dopKey];
                    } else {
                        $dop = DailyOperationPlan::where('tanggal', $tanggal)
                            ->where('pekerjaan', $row[$idxPekerjaan] ?? '')
                            ->where('unit_id', $row[$idxUnitId] ?? '')
                            ->where('lokasi', $row[$idxLokasi] ?? '')
                            ->first();

                        if (!$dop) {
                            $latitude = !empty($row[$idxLat]) ? (is_numeric($row[$idxLat]) ? (float)$row[$idxLat] : null) : null;
                            $longitude = !empty($row[$idxLong]) ? (is_numeric($row[$idxLong]) ? (float)$row[$idxLong] : null) : null;
                            if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
                                $latitude = null;
                            }
                            if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
                                $longitude = null;
                            }
                            $dop = DailyOperationPlan::create([
                                'tanggal' => $tanggal,
                                'pekerjaan' => $row[$idxPekerjaan] ?? '',
                                'aktivitas' => $idxAktivitas !== null ? ($row[$idxAktivitas] ?? null) : null,
                                'unit_id' => $row[$idxUnitId] ?? '',
                                'lokasi' => $row[$idxLokasi] ?? '',
                                'site' => $idxSite !== null ? ($row[$idxSite] ?? null) : null,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'detail_lokasi' => $row[$idxDetailLokasi] ?? null,
                                'potensi_resiko' => $row[$idxPotensi] ?? null,
                                'pengendalian_bahaya' => $row[$idxPengendalian] ?? null,
                                'catatan' => $row[$idxCatatan] ?? null,
                                'foto_pekerjaan' => null,
                            ]);
                            $imported++;
                        }

                        $dopCache[$dopKey] = $dop;

                        if (!empty($row[$idxCctv])) {
                            $cctvIds = array_map('trim', explode(',', $row[$idxCctv]));
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

                    if (!empty($row[$idxPicShift]) && !empty($row[$idxPicNama])) {
                        $picExists = DopPicBerauCoal::where('dop_id', $dop->id)
                            ->where('shift', $row[$idxPicShift] ?? null)
                            ->where('nama_pic', $row[$idxPicNama] ?? '')
                            ->exists();
                        if (!$picExists) {
                            DopPicBerauCoal::create([
                                'dop_id' => $dop->id,
                                'shift' => $row[$idxPicShift] ?? null,
                                'nama_pic' => $row[$idxPicNama] ?? '',
                                'layer' => $row[$idxPicLayer] ?? null,
                            ]);
                        }
                    }

                    if (!empty($row[$idxPengawasShift]) && !empty($row[$idxPengawasNama])) {
                        $pengawasExists = DopPengawasMitraKerja::where('dop_id', $dop->id)
                            ->where('shift', $row[$idxPengawasShift] ?? null)
                            ->where('nama_pengawas', $row[$idxPengawasNama] ?? '')
                            ->exists();
                        if (!$pengawasExists) {
                            DopPengawasMitraKerja::create([
                                'dop_id' => $dop->id,
                                'shift' => $row[$idxPengawasShift] ?? null,
                                'nama_pengawas' => $row[$idxPengawasNama] ?? '',
                                'layer' => $row[$idxPengawasLayer] ?? null,
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
