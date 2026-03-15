<?php

namespace App\Http\Controllers;

use App\Models\UnitMtd;
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

class UnitMtdController extends Controller
{
    public function index(): View
    {
        $items = UnitMtd::orderBy('site')->orderBy('perusahaan')->orderBy('no_unit')->orderBy('id')->get();
        return view('unit-mtd.index', compact('items'));
    }

    public function create(): View
    {
        return view('unit-mtd.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeNumericInput($request);
        $request->validate([
            'site' => 'nullable|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'no_unit' => 'nullable|string|max:100',
            'mtd' => 'nullable|numeric',
            'avg_per_day' => 'nullable|numeric',
        ]);

        UnitMtd::create([
            'site' => $request->site,
            'perusahaan' => $request->perusahaan,
            'kategori' => $request->kategori,
            'no_unit' => $request->no_unit,
            'mtd' => $request->filled('mtd') ? $request->mtd : null,
            'avg_per_day' => $request->filled('avg_per_day') ? $request->avg_per_day : null,
        ]);

        return redirect()->route('unit-mtd.index')->with('success', 'Data berhasil ditambah.');
    }

    public function edit(int $id): View
    {
        $item = UnitMtd::findOrFail($id);
        return view('unit-mtd.edit', compact('item'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = UnitMtd::findOrFail($id);
        $this->normalizeNumericInput($request);
        $request->validate([
            'site' => 'nullable|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'no_unit' => 'nullable|string|max:100',
            'mtd' => 'nullable|numeric',
            'avg_per_day' => 'nullable|numeric',
        ]);

        $item->update([
            'site' => $request->site,
            'perusahaan' => $request->perusahaan,
            'kategori' => $request->kategori,
            'no_unit' => $request->no_unit,
            'mtd' => $request->filled('mtd') ? $request->mtd : null,
            'avg_per_day' => $request->filled('avg_per_day') ? $request->avg_per_day : null,
        ]);

        return redirect()->route('unit-mtd.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = UnitMtd::findOrFail($id);
        $item->delete();
        return redirect()->route('unit-mtd.index')->with('success', 'Data berhasil dihapus.');
    }

    public function importForm(): View
    {
        return view('unit-mtd.import');
    }

    public function downloadTemplate()
    {
        $headers = ['Site', 'Perusahaan', 'Kategori', 'No Unit', 'MTD', 'AVG per Day'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', 'BMO 2');
        $sheet->setCellValue('B2', 'PAMA');
        $sheet->setCellValue('C2', 'Wheel loader');
        $sheet->setCellValue('D2', 'BRBMWE201');
        $sheet->setCellValue('E2', 204.1);
        $sheet->setCellValue('F2', 204.10);

        $sheet->setCellValue('A3', 'BMO 2');
        $sheet->setCellValue('B3', 'PAMA');
        $sheet->setCellValue('C3', 'Bulldozers');
        $sheet->setCellValue('D3', 'DZ1169');
        $sheet->setCellValue('E3', 2828);
        $sheet->setCellValue('F3', 565.60);

        $sheet->setCellValue('A4', 'BMO 2');
        $sheet->setCellValue('B4', 'PAMA');
        $sheet->setCellValue('C4', 'Bulldozers');
        $sheet->setCellValue('D4', 'DZ1187');
        $sheet->setCellValue('E4', 6211);
        $sheet->setCellValue('F4', 776.38);

        foreach (range('A', 'F') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'unit_mtd_template_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Replace comma with dot for mtd and avg_per_day so validation accepts them.
     */
    private function normalizeNumericInput(Request $request): void
    {
        if ($request->has('mtd') && is_string($request->mtd)) {
            $request->merge(['mtd' => str_replace(',', '.', $request->mtd)]);
        }
        if ($request->has('avg_per_day') && is_string($request->avg_per_day)) {
            $request->merge(['avg_per_day' => str_replace(',', '.', $request->avg_per_day)]);
        }
    }

    /**
     * Parse numeric value from Excel (supports comma as decimal separator).
     */
    private function parseNumeric($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $str = trim((string) $value);
        $str = str_replace(',', '.', $str);
        return is_numeric($str) ? (float) $str : null;
    }

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

                $site = isset($row[0]) ? trim((string) $row[0]) : null;
                $perusahaan = isset($row[1]) ? trim((string) $row[1]) : null;
                $kategori = isset($row[2]) ? trim((string) $row[2]) : null;
                $noUnit = isset($row[3]) ? trim((string) $row[3]) : null;
                $mtd = $this->parseNumeric($row[4] ?? null);
                $avgPerDay = $this->parseNumeric($row[5] ?? null);

                UnitMtd::create([
                    'site' => $site ?: null,
                    'perusahaan' => $perusahaan ?: null,
                    'kategori' => $kategori ?: null,
                    'no_unit' => $noUnit ?: null,
                    'mtd' => $mtd,
                    'avg_per_day' => $avgPerDay,
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
            return redirect()->route('unit-mtd.index')->with('success', $msg);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('UnitMtd import: ' . $e->getMessage());
            return redirect()->route('unit-mtd.import-form')->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
