<?php

namespace App\Http\Controllers;

use App\Jobs\ImportInsidenLpiJob;
use App\Models\InsidenCcr;
use App\Models\InsidenLpi;
use App\Models\InsidenLpiLayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InsidenLpiController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = InsidenLpi::query()->with(['insidenCcr', 'layers'])->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_kecelakaan', 'like', '%' . $search . '%')
                    ->orWhere('nama', 'like', '%' . $search . '%')
                    ->orWhere('perusahaan', 'like', '%' . $search . '%')
                    ->orWhere('kronologis', 'like', '%' . $search . '%');
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        if ($site = $request->get('site')) {
            $query->where('site', $site);
        }

        if ($statusLpi = $request->get('status_lpi')) {
            $query->where('status_lpi', $statusLpi);
        }

        if ($insidenCcrId = $request->get('insiden_ccr_id')) {
            $query->where('insiden_ccr_id', $insidenCcrId);
        }

        $insidens = $query->paginate($perPage)->withQueryString();

        // Get unique values for filter dropdowns
        $kategoriList = InsidenLpi::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori');
        $siteList = InsidenLpi::select('site')->distinct()->whereNotNull('site')->pluck('site');
        $statusLpiList = InsidenLpi::select('status_lpi')->distinct()->whereNotNull('status_lpi')->pluck('status_lpi');
        
        // Get insiden CCR list for dropdown
        $insidenCcrList = InsidenCcr::select('id', 'ccr_id', 'ccr_jenis_insiden', 'ccr_waktu_insiden', 'ccr_site')
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();

        return view('insiden-lpi.index', compact(
            'insidens', 'perPage', 'search', 
            'kategoriList', 'siteList', 'statusLpiList', 'insidenCcrList'
        ));
    }

    public function create(Request $request): View
    {
        $insiden = new InsidenLpi();
        
        // Get insiden CCR list for dropdown
        $insidenCcrList = InsidenCcr::select('id', 'ccr_id', 'ccr_jenis_insiden', 'ccr_waktu_insiden', 'ccr_site', 'ccr_kronologi')
            ->orderBy('id', 'desc')
            ->get();
        
        // Pre-select insiden_ccr_id if provided
        $selectedCcrId = $request->get('insiden_ccr_id');

        return view('insiden-lpi.create', compact('insiden', 'insidenCcrList', 'selectedCcrId'));
    }

    /**
     * Get CCR data for auto-fill via AJAX
     */
    public function getCcrData(InsidenCcr $insidenCcr): JsonResponse
    {
        $waktuInsiden = $insidenCcr->ccr_waktu_insiden;
        
        return response()->json([
            'success' => true,
            'data' => [
                'no_kecelakaan' => $insidenCcr->no_kecelakaan,
                'tanggal' => $waktuInsiden?->day,
                'bulan' => $waktuInsiden?->month,
                'tahun' => $waktuInsiden?->year,
                'jam' => $waktuInsiden?->hour,
                'menit' => $waktuInsiden?->minute,
                'site' => $insidenCcr->ccr_site,
                'lokasi' => $insidenCcr->ccr_lokasi,
                'sublokasi' => $insidenCcr->ccr_detil_lokasi,
                'perusahaan' => $insidenCcr->ccr_lokasi_perusahaan ?: $insidenCcr->ccr_perusahaan_pelapor,
                'kategori' => $insidenCcr->ccr_jenis_insiden,
                'kronologis' => $insidenCcr->ccr_kronologi,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $layersData = $request->input('layers', []);

        DB::transaction(function () use ($data, $layersData) {
            $insiden = InsidenLpi::create($data);
            
            // Save layers
            foreach ($layersData as $layer) {
                if (!empty($layer['layer']) || !empty($layer['jenis_item_ipls']) || !empty($layer['detail_layer']) || !empty($layer['keterangan_layer'])) {
                    $insiden->layers()->create([
                        'layer' => $layer['layer'] ?? null,
                        'jenis_item_ipls' => $layer['jenis_item_ipls'] ?? null,
                        'detail_layer' => $layer['detail_layer'] ?? null,
                        'keterangan_layer' => $layer['keterangan_layer'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('insiden-lpi.index')->with('success', 'Data Insiden LPI berhasil disimpan.');
    }

    public function edit(InsidenLpi $insidenLpi): View
    {
        $insidenLpi->load('layers');
        
        $insidenCcrList = InsidenCcr::select('id', 'ccr_id', 'ccr_jenis_insiden', 'ccr_waktu_insiden', 'ccr_site', 'ccr_kronologi')
            ->orderBy('id', 'desc')
            ->get();

        return view('insiden-lpi.edit', ['insiden' => $insidenLpi, 'insidenCcrList' => $insidenCcrList]);
    }

    public function update(Request $request, InsidenLpi $insidenLpi): RedirectResponse
    {
        $data = $this->validatedData($request);
        $layersData = $request->input('layers', []);

        DB::transaction(function () use ($insidenLpi, $data, $layersData) {
            $insidenLpi->update($data);
            
            // Delete existing layers
            $insidenLpi->layers()->delete();
            
            // Save new layers
            foreach ($layersData as $layer) {
                if (!empty($layer['layer']) || !empty($layer['jenis_item_ipls']) || !empty($layer['detail_layer']) || !empty($layer['keterangan_layer'])) {
                    $insidenLpi->layers()->create([
                        'layer' => $layer['layer'] ?? null,
                        'jenis_item_ipls' => $layer['jenis_item_ipls'] ?? null,
                        'detail_layer' => $layer['detail_layer'] ?? null,
                        'keterangan_layer' => $layer['keterangan_layer'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('insiden-lpi.index')->with('success', 'Data Insiden LPI berhasil diperbarui.');
    }

    public function destroy(InsidenLpi $insidenLpi): RedirectResponse
    {
        $insidenLpi->delete();

        return redirect()->route('insiden-lpi.index')->with('success', 'Data Insiden LPI berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
            'insiden_ccr_id' => ['nullable', 'exists:insiden_ccr,id'],
        ]);

        $file = $request->file('excel_file');
        $name = uniqid('insiden_lpi_', true) . '.' . $file->getClientOriginalExtension();
        $storedPath = $file->storeAs('insiden-lpi-imports', $name);

        $insidenCcrId = $request->input('insiden_ccr_id');

        ImportInsidenLpiJob::dispatch($storedPath, $insidenCcrId);

        return redirect()->route('insiden-lpi.index')->with('success', 'File berhasil diunggah dan sedang diproses di background.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'No Kecelakaan',
            'Kode BeInvestigasi',
            'Status LPI',
            'Target Penyelesaian LPI',
            'Actual Penyelesaian LPI',
            'Ketepatan Waktu LPI',
            'Tanggal',
            'Bulan',
            'Tahun',
            'Minggu Ke',
            'Hari',
            'Jam',
            'Menit',
            'Shift',
            'Perusahaan',
            'Latitude',
            'Longitude',
            'Departemen',
            'Site',
            'Lokasi',
            'Sublokasi',
            'Lokasi Spesifik',
            'Lokasi (Validasi HSECM)',
            'PJA',
            'Insiden Terjadi Dalam Site Mining',
            'Kategori',
            'Injury/Non Injury',
            'Kronologis',
            'High Potential',
            'Alat Terlibat',
            'Nama',
            'Jabatan',
            'Shift kerja ke',
            'Hari kerja ke',
            'NPK',
            'Umur',
            'Range Umur (Tahun)',
            'Masa Kerja Perusahaan (Tahun)',
            'Masa Kerja Perusahaan (Bulan)',
            'Range Masa Kerja Perusahaan',
            'Masa Kerja di BC (Tahun)',
            'Masa Kerja di BC (Bulan)',
            'Range Masa Kerja BC (Tahun)',
            'Bagian Luka',
            'Loss Cost (Rp)',
            'Saksi Langsung',
            'Atasan Langsung',
            'Jabatan Struktural - Atasan Langsung',
            'Kontak',
            'Detail Kontak',
            'Sumber Kecelakaan',
            'Layer',
            'Jenis Item IPLS',
            'Detail Layer',
            'Keterangan Layer',
            'id Lokasi Insiden',
            'id PJA Insiden',
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_insiden_lpi.csv"',
        ]);
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'insiden_ccr_id' => ['nullable', 'exists:insiden_ccr,id'],
            'no_kecelakaan' => ['nullable', 'string', 'max:100'],
            'kode_be_investigasi' => ['nullable', 'string', 'max:100'],
            'status_lpi' => ['nullable', 'string', 'max:50'],
            'target_penyelesaian_lpi' => ['nullable', 'date'],
            'actual_penyelesaian_lpi' => ['nullable', 'date'],
            'ketepatan_waktu_lpi' => ['nullable', 'string', 'max:50'],
            'tanggal' => ['nullable', 'integer'],
            'bulan' => ['nullable', 'integer'],
            'tahun' => ['nullable', 'integer'],
            'minggu_ke' => ['nullable', 'integer'],
            'hari' => ['nullable', 'string', 'max:20'],
            'jam' => ['nullable', 'integer'],
            'menit' => ['nullable', 'integer'],
            'shift' => ['nullable', 'string', 'max:50'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'departemen' => ['nullable', 'string', 'max:255'],
            'site' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'sublokasi' => ['nullable', 'string', 'max:255'],
            'lokasi_spesifik' => ['nullable', 'string', 'max:500'],
            'lokasi_validasi_hsecm' => ['nullable', 'string', 'max:255'],
            'pja' => ['nullable', 'string', 'max:500'],
            'insiden_dalam_site_mining' => ['nullable', 'string', 'max:50'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'injury_status' => ['nullable', 'string', 'max:50'],
            'kronologis' => ['nullable', 'string'],
            'high_potential' => ['nullable', 'string', 'max:50'],
            'alat_terlibat' => ['nullable', 'string', 'max:255'],
            'nama' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'shift_kerja_ke' => ['nullable', 'string', 'max:50'],
            'hari_kerja_ke' => ['nullable', 'integer'],
            'npk' => ['nullable', 'string', 'max:50'],
            'umur' => ['nullable', 'integer'],
            'range_umur' => ['nullable', 'string', 'max:50'],
            'masa_kerja_perusahaan_tahun' => ['nullable', 'integer'],
            'masa_kerja_perusahaan_bulan' => ['nullable', 'integer'],
            'range_masa_kerja_perusahaan' => ['nullable', 'string', 'max:50'],
            'masa_kerja_bc_tahun' => ['nullable', 'integer'],
            'masa_kerja_bc_bulan' => ['nullable', 'integer'],
            'range_masa_kerja_bc' => ['nullable', 'string', 'max:50'],
            'bagian_luka' => ['nullable', 'string', 'max:255'],
            'loss_cost' => ['nullable', 'numeric'],
            'saksi_langsung' => ['nullable', 'string', 'max:255'],
            'atasan_langsung' => ['nullable', 'string', 'max:255'],
            'jabatan_atasan_langsung' => ['nullable', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'detail_kontak' => ['nullable', 'string'],
            'sumber_kecelakaan' => ['nullable', 'string', 'max:255'],
            'id_lokasi_insiden' => ['nullable', 'string', 'max:100'],
            'id_pja_insiden' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
