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
            $validated['foto_pekerjaan'] = $fotoPath;
        } else {
            // Keep existing photo if not updated
            unset($validated['foto_pekerjaan']);
        }

        // Update DOP
        $dop->update($validated);

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
}

