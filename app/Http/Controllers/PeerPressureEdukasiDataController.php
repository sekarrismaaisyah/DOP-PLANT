<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PeerPressureEdukasiDataRequest;
use App\Models\PeerPressureKejadianEdukasi;
use App\Models\PeerPressurePesertaEdukasi;
use App\Services\PeerPressure\PeerPressureKaryawanNitipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeerPressureEdukasiDataController extends Controller
{
    public function index(Request $request, PeerPressureKaryawanNitipService $karyawanNitip): View
    {
        $query = PeerPressureKejadianEdukasi::query()
            ->with('peserta')
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('id');

        $q = trim((string) $request->get('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('perusahaan', 'like', '%' . $q . '%')
                    ->orWhere('lokasi_temuan', 'like', '%' . $q . '%')
                    ->orWhere('lokasi_edukasi', 'like', '%' . $q . '%')
                    ->orWhere('kronologi_temuan', 'like', '%' . $q . '%')
                    ->orWhere('kategori_deviasi', 'like', '%' . $q . '%')
                    ->orWhere('pemimpin_edukasi', 'like', '%' . $q . '%')
                    ->orWhere('status_pelaksanaan_edukasi', 'like', '%' . $q . '%')
                    ->orWhereHas('peserta', function ($p) use ($q) {
                        $p->where('sid', 'like', '%' . $q . '%')
                            ->orWhere('nama', 'like', '%' . $q . '%');
                    });
            });
        }

        $kejadian = $query->paginate(12)->withQueryString();

        $sids = [];
        foreach ($kejadian as $k) {
            foreach ($k->peserta as $p) {
                $sids[] = $p->sid;
            }
        }
        $peerFotoUrls = $karyawanNitip->fotoUrlsByKodeSids($sids);

        return view('peer-pressure-edukasi.data.index', [
            'kejadian' => $kejadian,
            'q' => $q,
            'navActive' => 'data',
            'peerFotoUrls' => $peerFotoUrls,
        ]);
    }

    public function create(): View
    {
        return view('peer-pressure-edukasi.data.form', [
            'mode' => 'create',
            'kejadian' => new PeerPressureKejadianEdukasi([
                'durasi_edukasi_menit' => 0,
            ]),
            'pelanggarRows' => [['sid' => '', 'nama' => '']],
            'peerRows' => [['sid' => '', 'nama' => '']],
            'navActive' => 'data',
        ]);
    }

    public function store(PeerPressureEdukasiDataRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $k = PeerPressureKejadianEdukasi::query()->create($request->kejadianAttributes());
            foreach ($request->pesertaPayload() as $row) {
                PeerPressurePesertaEdukasi::query()->create([
                    'kejadian_edukasi_id' => $k->id,
                    'sid' => $row['sid'],
                    'nama' => $row['nama'],
                    'peran' => $row['peran'],
                    'urutan' => $row['urutan'],
                ]);
            }
        });

        return redirect()
            ->route('peer-pressure-edukasi.data.index')
            ->with('success', 'Data Peer Pressure berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $kejadian = PeerPressureKejadianEdukasi::query()
            ->with('peserta')
            ->findOrFail($id);

        $pelanggar = $kejadian->peserta->where('peran', 'pelanggar')->values();
        $peers = $kejadian->peserta->where('peran', 'peer')->values();

        $pelanggarRows = $pelanggar->isNotEmpty()
            ? $pelanggar->map(fn ($p) => ['sid' => $p->sid, 'nama' => $p->nama])->all()
            : [['sid' => '', 'nama' => '']];
        $peerRows = $peers->isNotEmpty()
            ? $peers->map(fn ($p) => ['sid' => $p->sid, 'nama' => $p->nama])->all()
            : [['sid' => '', 'nama' => '']];

        return view('peer-pressure-edukasi.data.form', [
            'mode' => 'edit',
            'kejadian' => $kejadian,
            'pelanggarRows' => $pelanggarRows,
            'peerRows' => $peerRows,
            'navActive' => 'data',
        ]);
    }

    public function update(PeerPressureEdukasiDataRequest $request, int $id): RedirectResponse
    {
        $kejadian = PeerPressureKejadianEdukasi::query()->findOrFail($id);

        DB::transaction(function () use ($request, $kejadian): void {
            $kejadian->update($request->kejadianAttributes());
            $kejadian->peserta()->delete();
            foreach ($request->pesertaPayload() as $row) {
                PeerPressurePesertaEdukasi::query()->create([
                    'kejadian_edukasi_id' => $kejadian->id,
                    'sid' => $row['sid'],
                    'nama' => $row['nama'],
                    'peran' => $row['peran'],
                    'urutan' => $row['urutan'],
                ]);
            }
        });

        return redirect()
            ->route('peer-pressure-edukasi.data.index')
            ->with('success', 'Data Peer Pressure berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kejadian = PeerPressureKejadianEdukasi::query()->findOrFail($id);
        $kejadian->delete();

        return redirect()
            ->route('peer-pressure-edukasi.data.index')
            ->with('success', 'Data Peer Pressure berhasil dihapus.');
    }
}
