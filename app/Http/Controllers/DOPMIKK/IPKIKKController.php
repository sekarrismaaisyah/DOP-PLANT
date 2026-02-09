<?php

namespace App\Http\Controllers\DOPMIKK;

use App\Http\Controllers\Controller;
use App\Jobs\ImportIpkIkkJob;
use App\Models\IpkIkk;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IPKIKKController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = IpkIkk::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengawas', 'like', "%{$search}%")
                    ->orWhere('kode_sid', 'like', "%{$search}%")
                    ->orWhere('kode_ikk', 'like', "%{$search}%")
                    ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                    ->orWhere('site', 'like', "%{$search}%")
                    ->orWhere('nama_pekerjaan', 'like', "%{$search}%")
                    ->orWhere('status_pekerjaan', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('dopmikk.ipk-ikk.index', compact('entries', 'perPage'));
    }

    public function create(): View
    {
        return view('dopmikk.ipk-ikk.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pengawas' => ['nullable', 'string', 'max:500'],
            'kode_sid' => ['nullable', 'string', 'max:100'],
            'kode_ikk' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['nullable', 'string', 'max:500'],
            'site' => ['nullable', 'string', 'max:255'],
            'durasi_jam' => ['nullable', 'string', 'max:50'],
            'kategori_ijk' => ['nullable', 'string', 'max:255'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:500'],
            'status_pekerjaan' => ['nullable', 'string', 'max:100'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if ($request->filled('ts')) {
            $validated['ts'] = Carbon::parse($request->ts);
        }

        IpkIkk::create($validated);

        return redirect()
            ->route('dopmikk.ipk-ikk.index')
            ->with('success', 'Data IPK-IKK berhasil disimpan.');
    }

    public function edit(IpkIkk $ipkIkk): View
    {
        return view('dopmikk.ipk-ikk.edit', compact('ipkIkk'));
    }

    public function update(Request $request, IpkIkk $ipkIkk): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pengawas' => ['nullable', 'string', 'max:500'],
            'kode_sid' => ['nullable', 'string', 'max:100'],
            'kode_ikk' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['nullable', 'string', 'max:500'],
            'site' => ['nullable', 'string', 'max:255'],
            'durasi_jam' => ['nullable', 'string', 'max:50'],
            'kategori_ijk' => ['nullable', 'string', 'max:255'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:500'],
            'status_pekerjaan' => ['nullable', 'string', 'max:100'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if ($request->filled('ts')) {
            $validated['ts'] = Carbon::parse($request->ts);
        }

        $ipkIkk->update($validated);

        return redirect()
            ->route('dopmikk.ipk-ikk.index')
            ->with('success', 'Data IPK-IKK berhasil diperbarui.');
    }

    public function destroy(IpkIkk $ipkIkk): RedirectResponse
    {
        $ipkIkk->delete();

        return redirect()
            ->route('dopmikk.ipk-ikk.index')
            ->with('success', 'Data IPK-IKK berhasil dihapus.');
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
                    ->route('dopmikk.ipk-ikk.index')
                    ->with('error', 'File tidak valid.');
            }

            $uniqueName = uniqid('ipk_ikk_', true) . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs('ipk-ikk-imports', $uniqueName);
            if (!$storedPath) {
                return redirect()
                    ->route('dopmikk.ipk-ikk.index')
                    ->with('error', 'Gagal menyimpan file.');
            }

            ImportIpkIkkJob::dispatch($storedPath)->onQueue('default');

            return redirect()
                ->route('dopmikk.ipk-ikk.index')
                ->with('success', 'File berhasil diunggah dan sedang diproses.');
        } catch (\Exception $e) {
            \Log::error('IPKIKKController import: ' . $e->getMessage());
            return redirect()
                ->route('dopmikk.ipk-ikk.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import IPK-IKK.
     */
    public function downloadTemplate()
    {
        $skip = ['id', 'created_at', 'updated_at'];
        $headers = array_values(array_diff(Schema::getColumnListing('ipk_ikk'), $skip));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('IPK-IKK');
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_ipk_ikk.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
