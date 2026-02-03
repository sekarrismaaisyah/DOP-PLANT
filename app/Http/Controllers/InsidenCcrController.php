<?php

namespace App\Http\Controllers;

use App\Jobs\ImportInsidenCcrJob;
use App\Models\InsidenCcr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsidenCcrController extends Controller
{
    public function index(Request $request): View
    {
        $query = InsidenCcr::query()->latest();

        // Apply server-side filters (these work with DataTables client-side search)
        if ($jenisInsiden = $request->get('jenis_insiden')) {
            $query->where('ccr_jenis_insiden', $jenisInsiden);
        }

        if ($site = $request->get('site')) {
            $query->where('ccr_site', $site);
        }

        if ($status = $request->get('status')) {
            $query->where('ccr_status', $status);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('ccr_waktu_insiden', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('ccr_waktu_insiden', '<=', $dateTo);
        }

        // Get all data (DataTables handles pagination client-side)
        $insidens = $query->get();

        // Get unique values for filter dropdowns
        $jenisInsidenList = InsidenCcr::select('ccr_jenis_insiden')->distinct()->whereNotNull('ccr_jenis_insiden')->pluck('ccr_jenis_insiden');
        $siteList = InsidenCcr::select('ccr_site')->distinct()->whereNotNull('ccr_site')->pluck('ccr_site');
        $statusList = InsidenCcr::select('ccr_status')->distinct()->whereNotNull('ccr_status')->pluck('ccr_status');

        return view('insiden-ccr.index', compact('insidens', 'jenisInsidenList', 'siteList', 'statusList'));
    }

    public function create(): View
    {
        $insiden = new InsidenCcr();
        return view('insiden-ccr.create', compact('insiden'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        InsidenCcr::create($data);

        return redirect()->route('insiden-ccr.index')->with('success', 'Data insiden CCR berhasil disimpan.');
    }

    public function edit(InsidenCcr $insidenCcr): View
    {
        return view('insiden-ccr.edit', ['insiden' => $insidenCcr]);
    }

    public function update(Request $request, InsidenCcr $insidenCcr): RedirectResponse
    {
        $data = $this->validatedData($request);
        $insidenCcr->update($data);

        return redirect()->route('insiden-ccr.index')->with('success', 'Data insiden CCR berhasil diperbarui.');
    }

    public function destroy(InsidenCcr $insidenCcr): RedirectResponse
    {
        $insidenCcr->delete();

        return redirect()->route('insiden-ccr.index')->with('success', 'Data insiden CCR berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ]);

        $file = $request->file('excel_file');
        $name = uniqid('insiden_ccr_', true) . '.' . $file->getClientOriginalExtension();
        $storedPath = $file->storeAs('insiden-ccr-imports', $name);

        ImportInsidenCcrJob::dispatch($storedPath);

        return redirect()->route('insiden-ccr.index')->with('success', 'File berhasil diunggah dan sedang diproses di background.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'ccr_id',
            'no_kecelakaan',
            'ccr_jenis_insiden',
            'ccr_waktu_pelaporan',
            'ccr_waktu_insiden',
            'ccr_kronologi',
            'ccr_nama_call_taker',
            'ccr_perusahaan_call_taker',
            'ccr_nama_pelapor',
            'ccr_perusahaan_pelapor',
            'ccr_lokasi_perusahaan',
            'ccr_site',
            'ccr_lokasi',
            'ccr_detil_lokasi',
            'ccr_keterangan_lokasi',
            'ccr_status',
            'ccr_pic_investigasi',
            'ccr_pic_investigasi_perusahaan',
            'ket_not_investigasi',
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            // Sample row
            fputcsv($file, [
                '1969',
                '',
                'Work Incident',
                '01/02/2026 11:10',
                '01/02/2026 11:05',
                'Kronologi insiden...',
                'Nama Call Taker',
                'PT ABC',
                'Nama Pelapor',
                'PT XYZ',
                'PT ABC',
                'SMO',
                'Road Management',
                'Detail lokasi',
                'Keterangan lokasi',
                'INSIDEN BARU',
                'PIC Investigasi',
                'PT ABC',
                '',
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_insiden_ccr.csv"',
        ]);
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'ccr_id' => ['nullable', 'string', 'max:50'],
            'no_kecelakaan' => ['nullable', 'string', 'max:100'],
            'ccr_jenis_insiden' => ['nullable', 'string', 'max:255'],
            'ccr_waktu_pelaporan' => ['nullable', 'date'],
            'ccr_waktu_insiden' => ['nullable', 'date'],
            'ccr_kronologi' => ['nullable', 'string'],
            'ccr_nama_call_taker' => ['nullable', 'string', 'max:255'],
            'ccr_perusahaan_call_taker' => ['nullable', 'string', 'max:255'],
            'ccr_nama_pelapor' => ['nullable', 'string', 'max:255'],
            'ccr_perusahaan_pelapor' => ['nullable', 'string', 'max:255'],
            'ccr_lokasi_perusahaan' => ['nullable', 'string', 'max:255'],
            'ccr_site' => ['nullable', 'string', 'max:100'],
            'ccr_lokasi' => ['nullable', 'string', 'max:255'],
            'ccr_detil_lokasi' => ['nullable', 'string', 'max:500'],
            'ccr_keterangan_lokasi' => ['nullable', 'string'],
            'ccr_status' => ['nullable', 'string', 'max:100'],
            'ccr_pic_investigasi' => ['nullable', 'string', 'max:255'],
            'ccr_pic_investigasi_perusahaan' => ['nullable', 'string', 'max:255'],
            'ket_not_investigasi' => ['nullable', 'string'],
        ]);
    }
}
