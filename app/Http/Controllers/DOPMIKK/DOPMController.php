<?php

namespace App\Http\Controllers\DOPMIKK;

use App\Http\Controllers\Controller;
use App\Jobs\ImportDopmJob;
use App\Models\Dopm;
use App\Models\IpkIkk;
use App\Models\Okk;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DOPMController extends Controller
{
    /**
     * Dashboard statistik harian DOPM, IKK, OKK, OAK. Semua tampilan by tanggal terpilih.
     */
    public function dashboard(Request $request): View
    {
        $filterDate = $request->get('date', now()->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
            $filterDate = now()->toDateString();
        }

        // Data harian: hitung per tanggal terpilih
        $totalDopmHarian = Dopm::where(function ($q) use ($filterDate) {
            $q->whereDate('tanggal_dop', $filterDate)->orWhereDate('timestamp', $filterDate);
        })->count();

        $totalIkkHarian = IpkIkk::whereDate('ts', $filterDate)->count();
        $totalOkkHarian = Okk::whereDate('ts', $filterDate)->count();

        $totalOakHarian = null;
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query')) {
                    $result = $clickHouse->query(
                        "SELECT count() as cnt FROM hse_automation.aaj_vw_car_oak_register_ytd_only WHERE toDate(submit_date) = '" . addslashes($filterDate) . "'"
                    );
                    $totalOakHarian = isset($result[0]['cnt']) ? (int) $result[0]['cnt'] : null;
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard OAK harian skip: ' . $e->getMessage());
        }

        // Daftar DOPM untuk tanggal terpilih (tampil langsung)
        $dopmListHarian = Dopm::where(function ($q) use ($filterDate) {
            $q->whereDate('tanggal_dop', $filterDate)->orWhereDate('timestamp', $filterDate);
        })->orderBy('tanggal_dop')->orderBy('id')->get();

        // Status matriks per DOPM: Is IKK ada IPK, Is IKK ada OKK (by kode_ikk + filterDate)
        $kodeIkks = $dopmListHarian->pluck('kode_ikk')->filter()->unique()->values()->all();
        $hasIpkByKode = [];
        $hasOkkByKode = [];
        if (!empty($kodeIkks)) {
            $ipkKodes = IpkIkk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            $okkKodes = Okk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            foreach ($kodeIkks as $k) {
                $hasIpkByKode[$k] = isset($ipkKodes[$k]);
                $hasOkkByKode[$k] = isset($okkKodes[$k]);
            }
        }

        foreach ($dopmListHarian as $dopm) {
            $k = $dopm->kode_ikk;
            $hasIpk = ($k === null || $k === '') ? null : ($hasIpkByKode[$k] ?? false);
            $hasOkk = ($k === null || $k === '') ? null : ($hasOkkByKode[$k] ?? false);
            $dopm->status_matriks = self::hitungStatusMatriks($hasIpk, $hasOkk);
            $dopm->is_ikk_ada_ipk = $hasIpk;
            $dopm->is_ikk_ada_okk = $hasOkk;
        }

        // Persentase IKK yang ada IPK / ada OKK (dasar: jumlah IKK unik dari DOPM tanggal terpilih)
        $totalIkkUnikHarian = count($kodeIkks);
        $ikkAdaIpkCount = $totalIkkUnikHarian > 0 ? count(array_filter($hasIpkByKode)) : 0;
        $ikkAdaOkkCount = $totalIkkUnikHarian > 0 ? count(array_filter($hasOkkByKode)) : 0;
        $pctIkkAdaIpk = $totalIkkUnikHarian > 0 ? round($ikkAdaIpkCount / $totalIkkUnikHarian * 100, 1) : 0;
        $pctIkkAdaOkk = $totalIkkUnikHarian > 0 ? round($ikkAdaOkkCount / $totalIkkUnikHarian * 100, 1) : 0;

        return view('dopmikk.dopm.Dashboard', [
            'filterDate' => $filterDate,
            'totalDopmHarian' => $totalDopmHarian,
            'totalIkkHarian' => $totalIkkHarian,
            'totalOkkHarian' => $totalOkkHarian,
            'totalOakHarian' => $totalOakHarian,
            'dopmListHarian' => $dopmListHarian,
            'totalIkkUnikHarian' => $totalIkkUnikHarian,
            'pctIkkAdaIpk' => $pctIkkAdaIpk,
            'pctIkkAdaOkk' => $pctIkkAdaOkk,
            'ikkAdaIpkCount' => $ikkAdaIpkCount,
            'ikkAdaOkkCount' => $ikkAdaOkkCount,
        ]);
    }

    /**
     * API data untuk modal detail DOPM: IPK-IKK, OKK, OAK (by kode_ikk dan konteks).
     */
    public function getDetailModalData(Request $request): JsonResponse
    {
        $kodeIkk = $request->input('kode_ikk', '');
        $jenisIjin = $request->input('jenis_ijin_kerja_khusus', '');
        $namaLayer2 = $request->input('nama_layer_2', '');
        $namaLayer3 = $request->input('nama_layer_3', '');
        $namaLayer4 = $request->input('nama_layer_4', '');
        $sidLayer2 = $request->input('sid_layer_2', '');
        $sidLayer3 = $request->input('sid_layer_3', '');
        $sidLayer4 = $request->input('sid_layer_4', '');

        $ipkIkk = [];
        $okk = [];
        $oak = [];

        if ($kodeIkk !== '' && $kodeIkk !== null) {
            $ipkIkk = IpkIkk::where('kode_ikk', $kodeIkk)->orderByDesc('ts')->get()->map(function ($row) {
                return $row->toArray();
            })->values()->toArray();
            $okk = Okk::where('kode_ikk', $kodeIkk)->orderByDesc('ts')->get()->map(function ($row) {
                return $row->toArray();
            })->values()->toArray();
        }

        try {
            $fullMapsUrl = route('full-maps.api.ikk-modal-data') . '?' . http_build_query([
                'kode_ikk' => $kodeIkk,
                'jenis_ijin_kerja_khusus' => $jenisIjin,
                'nama_layer_2' => $namaLayer2,
                'nama_layer_3' => $namaLayer3,
                'nama_layer_4' => $namaLayer4,
                'sid_layer_2' => $sidLayer2,
                'sid_layer_3' => $sidLayer3,
                'sid_layer_4' => $sidLayer4,
            ]);
            $response = Http::timeout(15)->get($fullMapsUrl);
            if ($response->successful()) {
                $body = $response->json();
                $oak = $body['oak'] ?? [];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard modal OAK fetch: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'ipk_ikk' => $ipkIkk,
            'okk' => $okk,
            'oak' => $oak,
            'dopm_context' => [
                'nama_layer_2' => $namaLayer2,
                'nama_layer_3' => $namaLayer3,
                'nama_layer_4' => $namaLayer4,
            ],
        ]);
    }

    /**
     * API untuk modal Intervensi: ambil user vw_user yang match Layer 1 (nama_layer_1)
     * Kriteria sama seperti CctvDataController getUsersFromClickHouse: is_active=1, username/nama tidak kosong.
     * Digunakan untuk kirim pengingat WA isi IPK.
     */
    public function getLayer1Users(Request $request): JsonResponse
    {
        $namaLayer1 = trim((string) $request->input('nama_layer_1', ''));
        if ($namaLayer1 === '') {
            return response()->json(['success' => true, 'users' => []]);
        }

        try {
            $query = \Illuminate\Support\Facades\DB::table('vw_user')
                ->where('is_active', 1)
                ->whereNotNull('username')
                ->where('username', '!=', '')
                ->whereNotNull('nama')
                ->where('nama', '!=', '');

            $query->where(function ($q) use ($namaLayer1) {
                $q->where('nama', 'LIKE', '%' . $namaLayer1 . '%')
                    ->orWhere('username', 'LIKE', '%' . $namaLayer1 . '%');
            });

            $users = $query->select('id', 'username', 'nama', 'selular')
                ->orderBy('nama')
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    $selular = trim($row->selular ?? '');
                    return [
                        'id' => $row->id,
                        'username' => trim($row->username ?? ''),
                        'nama' => trim($row->nama ?? ''),
                        'selular' => $selular,
                    ];
                })
                ->values()
                ->toArray();

            return response()->json(['success' => true, 'users' => $users]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DOPMController getLayer1Users: ' . $e->getMessage());
            return response()->json(['success' => false, 'users' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display DOPM entries with search and pagination.
     */
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = Dopm::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_dop', 'like', "%{$search}%")
                    ->orWhere('site_ijin_kerja_khusus', 'like', "%{$search}%")
                    ->orWhere('perusahaan_ijin_kerja_khusus', 'like', "%{$search}%")
                    ->orWhere('kode_ikk', 'like', "%{$search}%")
                    ->orWhere('nama_pekerjaan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('nama_layer_2', 'like', "%{$search}%")
                    ->orWhere('nama_layer_3', 'like', "%{$search}%")
                    ->orWhere('nama_layer_4', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('dopmikk.dopm.index', compact('entries', 'perPage'));
    }

    /**
     * Show the form for creating a new DOPM (optional; can use import only).
     */
    public function create(): View
    {
        return view('dopmikk.dopm.create');
    }

    /**
     * Store a new DOPM entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_dop' => ['nullable', 'string', 'max:100'],
            'timestamp' => ['nullable', 'date'],
            'site_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'perusahaan_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'jenis_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'kode_ikk' => ['nullable', 'string', 'max:255'],
            'tanggal_selesai_ijin' => ['nullable', 'date'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:255'],
            'tanggal_dop' => ['nullable', 'date'],
            'status_pengiriman_notif' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'deskripsi_atau_alasan_cancel' => ['nullable', 'string'],
            'sid_layer_1' => ['nullable', 'string', 'max:255'],
            'nama_layer_1' => ['nullable', 'string', 'max:255'],
            'shift' => ['nullable', 'string', 'max:255'],
            'jam_mulai' => ['nullable', 'string', 'max:255'],
            'jam_akhir' => ['nullable', 'string', 'max:255'],
            'sid_layer_2' => ['nullable', 'string', 'max:255'],
            'nama_layer_2' => ['nullable', 'string', 'max:255'],
            'sid_layer_3' => ['nullable', 'string', 'max:255'],
            'nama_layer_3' => ['nullable', 'string', 'max:255'],
            'sid_layer_4' => ['nullable', 'string', 'max:255'],
            'nama_layer_4' => ['nullable', 'string', 'max:255'],
            'jenis_pengawasan_layer' => ['nullable', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if (!empty($validated['timestamp'])) {
            $validated['timestamp'] = Carbon::parse($validated['timestamp']);
        }

        Dopm::create($validated);

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified DOPM.
     */
    public function edit(Dopm $dopm): View
    {
        return view('dopmikk.dopm.edit', compact('dopm'));
    }

    /**
     * Update the specified DOPM.
     */
    public function update(Request $request, Dopm $dopm): RedirectResponse
    {
        $validated = $request->validate([
            'id_dop' => ['nullable', 'string', 'max:100'],
            'timestamp' => ['nullable', 'date'],
            'site_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'perusahaan_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'jenis_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'kode_ikk' => ['nullable', 'string', 'max:255'],
            'tanggal_selesai_ijin' => ['nullable', 'date'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:255'],
            'tanggal_dop' => ['nullable', 'date'],
            'status_pengiriman_notif' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'deskripsi_atau_alasan_cancel' => ['nullable', 'string'],
            'sid_layer_1' => ['nullable', 'string', 'max:255'],
            'nama_layer_1' => ['nullable', 'string', 'max:255'],
            'shift' => ['nullable', 'string', 'max:255'],
            'jam_mulai' => ['nullable', 'string', 'max:255'],
            'jam_akhir' => ['nullable', 'string', 'max:255'],
            'sid_layer_2' => ['nullable', 'string', 'max:255'],
            'nama_layer_2' => ['nullable', 'string', 'max:255'],
            'sid_layer_3' => ['nullable', 'string', 'max:255'],
            'nama_layer_3' => ['nullable', 'string', 'max:255'],
            'sid_layer_4' => ['nullable', 'string', 'max:255'],
            'nama_layer_4' => ['nullable', 'string', 'max:255'],
            'jenis_pengawasan_layer' => ['nullable', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if (!empty($validated['timestamp'])) {
            $validated['timestamp'] = Carbon::parse($validated['timestamp']);
        }

        $dopm->update($validated);

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil diperbarui.');
    }

    /**
     * Remove the specified DOPM.
     */
    public function destroy(Dopm $dopm): RedirectResponse
    {
        $dopm->delete();

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil dihapus.');
    }

    /**
     * Import DOPM data from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            ]);

            $file = $request->file('excel_file');

            if (!$file || !$file->isValid()) {
                return redirect()
                    ->route('dopmikk.dopm.index')
                    ->with('error', 'File tidak valid atau gagal diunggah.');
            }

            $uniqueName = uniqid('dopm_', true) . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs('dopm-imports', $uniqueName);

            if (!$storedPath) {
                return redirect()
                    ->route('dopmikk.dopm.index')
                    ->with('error', 'Gagal menyimpan file.');
            }

            ImportDopmJob::dispatch($storedPath)->onQueue('default');

            return redirect()
                ->route('dopmikk.dopm.index')
                ->with('success', 'File berhasil diunggah dan sedang diproses. Silakan cek beberapa saat lagi.');
        } catch (\Exception $e) {
            \Log::error('DOPMController import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('dopmikk.dopm.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import DOPM.
     */
    public function downloadTemplate()
    {
        // Urutan harus sama dengan ImportDopmJob
        $headers = [
            'ID',
            'Timestamp',
            'Site Ijin Kerja Khusus',
            'Perusahaan Ijin Kerja Khusus',
            'Jenis Ijin Kerja Khusus',
            'Kode IKK (Ijin Kerja Khusus)',
            'Tanggal Selesai Ijin',
            'Nama Pekerjaan',
            'Tanggal DOP',
            'SID Layer 1',
            'Nama Layer 1',
            'Shift',
            'Jam Mulai',
            'Jam Akhir',
            'Status Pengiriman Notif',
            'Status',
            'Deskripsi / Alasan cancel',
            'SID Layer 2',
            'Nama Layer 2',
            'SID Layer 3',
            'Nama Layer 3',
            'SID Layer 4',
            'Nama Layer 4',
            'Jenis Pengawasan Layer 2,3,4',
            'Detail Lokasi',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DOPM');
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_dopm.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Hitung status matriks berdasarkan Is IKK ada IPK dan Is IKK ada OKK.
     * Returns: 'Hijau' | 'Kuning' | 'Merah'
     *
     * @param  bool|null  $isIkkAdaIpk
     * @param  bool|null  $isIkkAdaOkk
     */
    public static function hitungStatusMatriks(?bool $isIkkAdaIpk, ?bool $isIkkAdaOkk): string
    {
        $ipk = $isIkkAdaIpk;
        $okk = $isIkkAdaOkk;

        // IF [Is IKK ada Full ada IPK OKK]=TRUE then 'Hijau'
        if ($ipk === true && $okk === true) {
            return 'Hijau';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and [Is IKK ada OKK]=TRUE then 'Hijau' (same)
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=FALSE then 'Merah'
        if ($ipk === false && $okk === false) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=NULL and [Is IKK ada OKK]=FALSE then 'Merah'
        if ($ipk === null && $okk === false) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=NULL then 'Merah'
        if ($ipk === false && $okk === null) {
            return 'Merah';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) and ISNULL([Is IKK ada OKK]) then 'Merah'
        if ($ipk === null && $okk === null) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and [Is IKK ada OKK]=FALSE then 'Kuning'
        if ($ipk === true && $okk === false) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and ISNULL([Is IKK ada OKK]) then 'Kuning'
        if ($ipk === true && $okk === null) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=TRUE then 'Kuning'
        if ($ipk === false && $okk === true) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) and [Is IKK ada OKK]=TRUE then 'Kuning'
        if ($ipk === null && $okk === true) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada OKK]=FALSE then 'Kuning'
        if ($okk === false) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE then 'Kuning'
        if ($ipk === false) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada OKK]) then 'Kuning'
        if ($okk === null) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) then 'Kuning'
        if ($ipk === null) {
            return 'Kuning';
        }
        // ELSE 'Merah'
        return 'Merah';
    }
}
