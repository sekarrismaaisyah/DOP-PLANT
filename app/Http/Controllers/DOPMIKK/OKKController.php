<?php

namespace App\Http\Controllers\DOPMIKK;

use App\Http\Controllers\Controller;
use App\Jobs\ImportOkkJob;
use App\Models\Okk;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OKKController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = Okk::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengawas', 'like', "%{$search}%")
                    ->orWhere('kode_sid', 'like', "%{$search}%")
                    ->orWhere('kode_ikk', 'like', "%{$search}%")
                    ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                    ->orWhere('site', 'like', "%{$search}%")
                    ->orWhere('layer_pengawas', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('dopmikk.okk.index', compact('entries', 'perPage'));
    }

    public function create(): View
    {
        return view('dopmikk.okk.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pengawas' => ['nullable', 'string', 'max:500'],
            'kode_sid' => ['nullable', 'string', 'max:100'],
            'kode_ikk' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['nullable', 'string', 'max:500'],
            'site' => ['nullable', 'string', 'max:255'],
            'jenis_ijk' => ['nullable', 'string', 'max:255'],
            'layer_pengawas' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('ts')) {
            $validated['ts'] = Carbon::parse($request->ts);
        }

        Okk::create($validated);

        return redirect()
            ->route('dopmikk.okk.index')
            ->with('success', 'Data OKK berhasil disimpan.');
    }

    public function edit(Okk $okk): View
    {
        return view('dopmikk.okk.edit', compact('okk'));
    }

    public function update(Request $request, Okk $okk): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pengawas' => ['nullable', 'string', 'max:500'],
            'kode_sid' => ['nullable', 'string', 'max:100'],
            'kode_ikk' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['nullable', 'string', 'max:500'],
            'site' => ['nullable', 'string', 'max:255'],
            'jenis_ijk' => ['nullable', 'string', 'max:255'],
            'layer_pengawas' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('ts')) {
            $validated['ts'] = Carbon::parse($request->ts);
        }

        $okk->update($validated);

        return redirect()
            ->route('dopmikk.okk.index')
            ->with('success', 'Data OKK berhasil diperbarui.');
    }

    public function destroy(Okk $okk): RedirectResponse
    {
        $okk->delete();

        return redirect()
            ->route('dopmikk.okk.index')
            ->with('success', 'Data OKK berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            ]);

            $file = $request->file('excel_file');
            if (!$file || !$file->isValid()) {
                return redirect()
                    ->route('dopmikk.okk.index')
                    ->with('error', 'File tidak valid.');
            }

            $uniqueName = uniqid('okk_', true) . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs('okk-imports', $uniqueName);
            if (!$storedPath) {
                return redirect()
                    ->route('dopmikk.okk.index')
                    ->with('error', 'Gagal menyimpan file.');
            }

            ImportOkkJob::dispatch($storedPath)->onQueue('default');

            return redirect()
                ->route('dopmikk.okk.index')
                ->with('success', 'File berhasil diunggah dan sedang diproses.');
        } catch (\Exception $e) {
            \Log::error('OKKController import: ' . $e->getMessage());
            return redirect()
                ->route('dopmikk.okk.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import OKK.
     */
    public function downloadTemplate()
    {
        $skip = ['id', 'created_at', 'updated_at'];
        $headers = array_values(array_diff(Schema::getColumnListing('okk'), $skip));

        if (empty($headers)) {
            return redirect()->route('dopmikk.okk.index')
                ->with('error', 'Tabel OKK belum memiliki kolom. Jalankan: php artisan migrate');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('OKK');
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_okk.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
