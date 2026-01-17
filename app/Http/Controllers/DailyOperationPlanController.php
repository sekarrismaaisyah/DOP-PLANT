<?php

namespace App\Http\Controllers;

use App\Models\DailyOperationPlan;
use App\Models\DopPicBerauCoal;
use App\Models\DopPengawasMitraKerja;
use App\Models\CctvData;
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

class DailyOperationPlanController extends Controller
{
    /**
     * Display a listing of the DOP entries.
     */
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $dops = DailyOperationPlan::with(['picBerauCoal', 'pengawasMitraKerja', 'cctvs'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('daily-operation-plan.index', compact('dops', 'perPage'));
    }

    /**
     * Show the form for creating a new DOP.
     */
    public function create(): View
    {
        $cctvs = CctvData::select('id', 'nama_cctv', 'no_cctv', 'lokasi_pemasangan')
            ->orderBy('nama_cctv')
            ->get();
        
        return view('daily-operation-plan.create', compact('cctvs'));
    }

    /**
     * Store a newly created DOP in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pekerjaan' => ['required', 'string', 'max:255'],
            'foto_pekerjaan' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // Max 5MB
            'unit_id' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
            'cctv_ids' => ['nullable', 'array'],
            'cctv_ids.*' => ['exists:cctv_data_bmo2,id'],
            'potensi_resiko' => ['nullable', 'string'],
            'pengendalian_bahaya' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
            // PIC Berau Coal
            'pic_berau_coal' => ['nullable', 'array'],
            'pic_berau_coal.*.shift' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            'pic_berau_coal.*.nama_pic' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            // Pengawas Mitra Kerja
            'pengawas_mitra_kerja' => ['nullable', 'array'],
            'pengawas_mitra_kerja.*.shift' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
            'pengawas_mitra_kerja.*.nama_pengawas' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
        ]);

        // Handle file upload
        $fotoPath = null;
        if ($request->hasFile('foto_pekerjaan')) {
            $fotoFile = $request->file('foto_pekerjaan');
            $fotoPath = $fotoFile->store('dop/foto-pekerjaan', 'public');
        }

        // Create DOP
        $dop = DailyOperationPlan::create([
            'pekerjaan' => $validated['pekerjaan'],
            'foto_pekerjaan' => $fotoPath,
            'unit_id' => $validated['unit_id'],
            'lokasi' => $validated['lokasi'],
            'detail_lokasi' => $validated['detail_lokasi'] ?? null,
            'potensi_resiko' => $validated['potensi_resiko'] ?? null,
            'pengendalian_bahaya' => $validated['pengendalian_bahaya'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'tanggal' => $validated['tanggal'],
        ]);

        // Sync CCTV
        if (!empty($validated['cctv_ids'])) {
            $dop->cctvs()->sync($validated['cctv_ids']);
        }

        // Create PIC Berau Coal entries
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

        // Create Pengawas Mitra Kerja entries
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
            ->route('daily-operation-plan.index')
            ->with('success', 'DOP berhasil disimpan.');
    }

    /**
     * Display the specified DOP.
     */
    public function show($id): View
    {
        $dop = DailyOperationPlan::with(['picBerauCoal', 'pengawasMitraKerja', 'cctvs'])->findOrFail($id);
        return view('daily-operation-plan.show', compact('dop'));
    }

    /**
     * Show the form for editing the specified DOP.
     */
    public function edit($id): View
    {
        $dop = DailyOperationPlan::with(['picBerauCoal', 'pengawasMitraKerja', 'cctvs'])->findOrFail($id);
        $cctvs = CctvData::select('id', 'nama_cctv', 'no_cctv', 'lokasi_pemasangan')
            ->orderBy('nama_cctv')
            ->get();
        
        return view('daily-operation-plan.edit', compact('dop', 'cctvs'));
    }

    /**
     * Update the specified DOP in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $dop = DailyOperationPlan::findOrFail($id);

        $validated = $request->validate([
            'pekerjaan' => ['required', 'string', 'max:255'],
            'foto_pekerjaan' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // Max 5MB
            'unit_id' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
            'cctv_ids' => ['nullable', 'array'],
            'cctv_ids.*' => ['exists:cctv_data_bmo2,id'],
            'potensi_resiko' => ['nullable', 'string'],
            'pengendalian_bahaya' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
            // PIC Berau Coal
            'pic_berau_coal' => ['nullable', 'array'],
            'pic_berau_coal.*.shift' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            'pic_berau_coal.*.nama_pic' => ['required_with:pic_berau_coal', 'string', 'max:255'],
            // Pengawas Mitra Kerja
            'pengawas_mitra_kerja' => ['nullable', 'array'],
            'pengawas_mitra_kerja.*.shift' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
            'pengawas_mitra_kerja.*.nama_pengawas' => ['required_with:pengawas_mitra_kerja', 'string', 'max:255'],
        ]);

        // Handle file upload
        if ($request->hasFile('foto_pekerjaan')) {
            // Delete old file if exists
            if ($dop->foto_pekerjaan) {
                Storage::disk('public')->delete($dop->foto_pekerjaan);
            }
            
            $fotoFile = $request->file('foto_pekerjaan');
            $fotoPath = $fotoFile->store('dop/foto-pekerjaan', 'public');
            // Add foto_pekerjaan path to validated data
            $validated['foto_pekerjaan'] = $fotoPath;
        }
        // If no file uploaded, foto_pekerjaan is not in validated array, so existing value will be preserved

        // Update DOP with validated data
        $dop->fill($validated);
        $dop->save();

        // Sync CCTV
        if (isset($validated['cctv_ids'])) {
            $dop->cctvs()->sync($validated['cctv_ids']);
        } else {
            $dop->cctvs()->sync([]);
        }

        // Delete existing PIC and Pengawas entries
        $dop->picBerauCoal()->delete();
        $dop->pengawasMitraKerja()->delete();

        // Create new PIC Berau Coal entries
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

        // Create new Pengawas Mitra Kerja entries
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
            ->route('daily-operation-plan.index')
            ->with('success', 'DOP berhasil diperbarui.');
    }

    /**
     * Remove the specified DOP from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $dop = DailyOperationPlan::findOrFail($id);

        // Delete photo if exists
        if ($dop->foto_pekerjaan) {
            Storage::disk('public')->delete($dop->foto_pekerjaan);
        }

        // Delete related entries (cascade should handle this, but being explicit)
        $dop->picBerauCoal()->delete();
        $dop->pengawasMitraKerja()->delete();

        // Delete DOP
        $dop->delete();

        return redirect()
            ->route('daily-operation-plan.index')
            ->with('success', 'DOP berhasil dihapus.');
    }

    /**
     * Download Excel template for DOP import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $headers = [
            'A1' => 'Tanggal',
            'B1' => 'Pekerjaan',
            'C1' => 'Unit ID',
            'D1' => 'Lokasi',
            'E1' => 'Detail Lokasi',
            'F1' => 'Potensi Risiko',
            'G1' => 'Pengendalian Bahaya',
            'H1' => 'Catatan',
            'I1' => 'CCTV IDs (pisahkan dengan koma)',
            'J1' => 'PIC Berau Coal - Shift',
            'K1' => 'PIC Berau Coal - Nama PIC',
            'L1' => 'PIC Berau Coal - Layer',
            'M1' => 'Pengawas Mitra Kerja - Shift',
            'N1' => 'Pengawas Mitra Kerja - Nama Pengawas',
            'O1' => 'Pengawas Mitra Kerja - Layer',
        ];

        // Set header values and style
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

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(25);
        $sheet->getColumnDimension('O')->setWidth(15);

        // Add example row
        $exampleRow = 2;
        $sheet->setCellValue('A' . $exampleRow, '2026-01-15');
        $sheet->setCellValue('B' . $exampleRow, 'Pemeliharaan Unit Excavator');
        $sheet->setCellValue('C' . $exampleRow, 'EX-001');
        $sheet->setCellValue('D' . $exampleRow, 'Area Tambang Utara');
        $sheet->setCellValue('E' . $exampleRow, 'Koordinat: -2.186253, 117.4539035');
        $sheet->setCellValue('F' . $exampleRow, 'Tenggelam, Terbalik');
        $sheet->setCellValue('G' . $exampleRow, 'Assessment, JSA, SOP');
        $sheet->setCellValue('H' . $exampleRow, 'Pekerjaan dilakukan pada shift pagi');
        $sheet->setCellValue('I' . $exampleRow, '1,2,3');
        $sheet->setCellValue('J' . $exampleRow, 'Shift 1 s/d 2');
        $sheet->setCellValue('K' . $exampleRow, 'John Doe');
        $sheet->setCellValue('L' . $exampleRow, 'Layer 1');
        $sheet->setCellValue('M' . $exampleRow, 'Shift 1 s/d 2');
        $sheet->setCellValue('N' . $exampleRow, 'Jane Smith');
        $sheet->setCellValue('O' . $exampleRow, 'Layer 2');

        // Add data validation notes
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

        // Freeze first row
        $sheet->freezePane('A2');

        // Create temporary file
        $filename = 'template_daily_operation_plan_' . date('Y-m-d') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import DOP from Excel file
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'], // Max 10MB
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row (row 1)
            $dataRows = array_slice($rows, 1);
            
            $imported = 0;
            $errors = [];
            $rowNumber = 2; // Start from row 2 (after header)
            $dopCache = []; // Cache untuk DOP yang sudah dibuat (untuk menggabungkan PIC/Pengawas)

            DB::beginTransaction();

            foreach ($dataRows as $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    $rowNumber++;
                    continue;
                }

                try {
                    // Validate required fields
                    if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3])) {
                        $errors[] = "Baris {$rowNumber}: Tanggal, Pekerjaan, Unit ID, dan Lokasi wajib diisi";
                        $rowNumber++;
                        continue;
                    }

                    // Parse tanggal
                    $tanggal = $row[0];
                    if (is_numeric($tanggal)) {
                        // Excel date serial number
                        $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
                    } else {
                        // Try to parse as date string
                        $parsedDate = date_create($tanggal);
                        if (!$parsedDate) {
                            throw new \Exception("Format tanggal tidak valid: {$tanggal}");
                        }
                        $tanggal = $parsedDate->format('Y-m-d');
                    }

                    // Create unique key untuk DOP (untuk mendeteksi DOP yang sama)
                    $dopKey = md5($tanggal . '|' . ($row[1] ?? '') . '|' . ($row[2] ?? '') . '|' . ($row[3] ?? ''));

                    // Check if DOP already exists in this import batch
                    if (isset($dopCache[$dopKey])) {
                        $dop = $dopCache[$dopKey];
                    } else {
                        // Check if DOP already exists in database
                        $dop = DailyOperationPlan::where('tanggal', $tanggal)
                            ->where('pekerjaan', $row[1] ?? '')
                            ->where('unit_id', $row[2] ?? '')
                            ->where('lokasi', $row[3] ?? '')
                            ->first();

                        if (!$dop) {
                            // Create new DOP
                            $dop = DailyOperationPlan::create([
                                'tanggal' => $tanggal,
                                'pekerjaan' => $row[1] ?? '',
                                'unit_id' => $row[2] ?? '',
                                'lokasi' => $row[3] ?? '',
                                'detail_lokasi' => $row[4] ?? null,
                                'potensi_resiko' => $row[5] ?? null,
                                'pengendalian_bahaya' => $row[6] ?? null,
                                'catatan' => $row[7] ?? null,
                                'foto_pekerjaan' => null, // Foto tidak bisa diupload via Excel
                            ]);
                            $imported++;
                        }

                        // Cache DOP
                        $dopCache[$dopKey] = $dop;

                        // Handle CCTV IDs (only on first occurrence)
                        if (!empty($row[8])) {
                            $cctvIds = array_map('trim', explode(',', $row[8]));
                            $cctvIds = array_filter($cctvIds, function($id) {
                                return is_numeric($id) && CctvData::where('id', $id)->exists();
                            });
                            if (!empty($cctvIds)) {
                                // Merge with existing CCTV IDs
                                $existingIds = $dop->cctvs()->pluck('id')->toArray();
                                $allIds = array_unique(array_merge($existingIds, $cctvIds));
                                $dop->cctvs()->sync($allIds);
                            }
                        }
                    }

                    // Handle PIC Berau Coal (always add, even if DOP exists)
                    if (!empty($row[9]) && !empty($row[10])) {
                        // Check if PIC already exists
                        $picExists = DopPicBerauCoal::where('dop_id', $dop->id)
                            ->where('shift', $row[9] ?? null)
                            ->where('nama_pic', $row[10] ?? '')
                            ->exists();
                        
                        if (!$picExists) {
                            DopPicBerauCoal::create([
                                'dop_id' => $dop->id,
                                'shift' => $row[9] ?? null,
                                'nama_pic' => $row[10] ?? '',
                                'layer' => $row[11] ?? null,
                            ]);
                        }
                    }

                    // Handle Pengawas Mitra Kerja (always add, even if DOP exists)
                    if (!empty($row[12]) && !empty($row[13])) {
                        // Check if Pengawas already exists
                        $pengawasExists = DopPengawasMitraKerja::where('dop_id', $dop->id)
                            ->where('shift', $row[12] ?? null)
                            ->where('nama_pengawas', $row[13] ?? '')
                            ->exists();
                        
                        if (!$pengawasExists) {
                            DopPengawasMitraKerja::create([
                                'dop_id' => $dop->id,
                                'shift' => $row[12] ?? null,
                                'nama_pengawas' => $row[13] ?? '',
                                'layer' => $row[14] ?? null,
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
                ->route('daily-operation-plan.index')
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

