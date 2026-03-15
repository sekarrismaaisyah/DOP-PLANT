<?php

namespace App\Http\Controllers;

use App\Models\Becomline;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BecomlineController extends Controller
{
    public function index(): View
    {
        $items = Becomline::orderBy('perusahaan_pemilik')->orderBy('site_operasional')->orderBy('id')->get();
        return view('becomline.index', compact('items'));
    }

    public function create(): View
    {
        return view('becomline.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'perusahaan_pemilik' => 'nullable|string|max:255',
            'site_operasional' => 'nullable|string|max:255',
            'jenis_unit_spip' => 'nullable|string|max:255',
            'expired' => 'nullable|date',
            'status_permit_spip' => 'nullable|string|max:100',
            'no_registrasi' => 'nullable|string|max:100',
        ]);

        Becomline::create([
            'perusahaan_pemilik' => $request->perusahaan_pemilik,
            'site_operasional' => $request->site_operasional,
            'jenis_unit_spip' => $request->jenis_unit_spip,
            'expired' => $request->filled('expired') ? $request->expired : null,
            'status_permit_spip' => $request->status_permit_spip,
            'no_registrasi' => $request->no_registrasi,
        ]);

        return redirect()->route('becomline.index')->with('success', 'Data berhasil ditambah.');
    }

    public function edit(int $id): View
    {
        $item = Becomline::findOrFail($id);
        return view('becomline.edit', compact('item'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = Becomline::findOrFail($id);

        $request->validate([
            'perusahaan_pemilik' => 'nullable|string|max:255',
            'site_operasional' => 'nullable|string|max:255',
            'jenis_unit_spip' => 'nullable|string|max:255',
            'expired' => 'nullable|date',
            'status_permit_spip' => 'nullable|string|max:100',
            'no_registrasi' => 'nullable|string|max:100',
        ]);

        $item->update([
            'perusahaan_pemilik' => $request->perusahaan_pemilik,
            'site_operasional' => $request->site_operasional,
            'jenis_unit_spip' => $request->jenis_unit_spip,
            'expired' => $request->filled('expired') ? $request->expired : null,
            'status_permit_spip' => $request->status_permit_spip,
            'no_registrasi' => $request->no_registrasi,
        ]);

        return redirect()->route('becomline.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = Becomline::findOrFail($id);
        $item->delete();
        return redirect()->route('becomline.index')->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Form upload Excel import.
     */
    public function importForm(): View
    {
        return view('becomline.import');
    }

    /**
     * Download template Excel (header + 2 baris contoh).
     */
    public function downloadTemplate()
    {
        $headers = ['Perusahaan Pemilik', 'Site Operasional', 'Jenis Unit SPIP', 'Expired', 'Status Permit SPIP', 'No Register'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $sheet->getStyle('D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', 'PT Bandang Mining Coal');
        $sheet->setCellValue('B2', 'BMO 2');
        $sheet->setCellValue('C2', 'A2B-TRACK - EXCAVATOR');
        $sheet->setCellValue('D2', 'Tuesday, 09 March 2027');
        $sheet->setCellValue('E2', 'PASSED');
        $sheet->setCellValue('F2', 'BMCEX-241');

        $sheet->setCellValue('A3', 'PT Madhani Talatah Nusantara');
        $sheet->setCellValue('B3', 'SMO');
        $sheet->setCellValue('C3', 'TRANSPORTASI SUPPORT');
        $sheet->setCellValue('D3', '');
        $sheet->setCellValue('E3', 'N/A');
        $sheet->setCellValue('F3', 'MTN-470');

        foreach (range('A', 'F') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'becomline_template_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import data dari Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
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
                if (empty(array_filter($row))) {
                    $rowNum++;
                    continue;
                }

                $perusahaan = isset($row[0]) ? trim((string) $row[0]) : '';
                $site = isset($row[1]) ? trim((string) $row[1]) : '';
                $jenisUnit = isset($row[2]) ? trim((string) $row[2]) : '';
                $expiredRaw = isset($row[3]) ? trim((string) $row[3]) : '';
                $status = isset($row[4]) ? trim((string) $row[4]) : '';
                $noRegistrasi = isset($row[5]) ? trim((string) $row[5]) : '';

                $expired = null;
                if ($expiredRaw !== '') {
                    try {
                        if (is_numeric($expiredRaw)) {
                            $expired = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($expiredRaw)->format('Y-m-d');
                        } else {
                            $expired = Carbon::parse($expiredRaw)->format('Y-m-d');
                        }
                    } catch (Exception $e) {
                        $errors[] = "Baris {$rowNum}: Format Expired tidak valid.";
                        $rowNum++;
                        continue;
                    }
                }

                Becomline::create([
                    'perusahaan_pemilik' => $perusahaan ?: null,
                    'site_operasional' => $site ?: null,
                    'jenis_unit_spip' => $jenisUnit ?: null,
                    'expired' => $expired,
                    'status_permit_spip' => $status ?: null,
                    'no_registrasi' => $noRegistrasi ?: null,
                ]);
                $imported++;
                $rowNum++;
            }

            DB::commit();

            $msg = "Import berhasil: {$imported} baris.";
            if (!empty($errors)) {
                $msg .= ' Error: ' . implode(' ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $msg .= ' ...';
                }
            }
            return redirect()->route('becomline.index')->with('success', $msg);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Becomline import: ' . $e->getMessage());
            return redirect()->route('becomline.import-form')->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
