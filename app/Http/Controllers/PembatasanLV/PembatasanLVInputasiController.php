<?php

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PembatasanLV\Concerns\ProvidesPembatasanLVLayout;
use App\Http\Requests\PembatasanLV\PembatasanLVInputasiLvRequest;
use App\Http\Requests\PembatasanLV\PembatasanLVInputasiOrangRequest;
use App\Models\BecomelineUnit;
use App\Models\CctvData;
use App\Models\MasterAktivitas;
use App\Models\PembatasanLvInputasi;
use App\Models\PembatasanOrangInputasi;
use App\Services\PembatasanLV\PembatasanLVKapasitasLokasiService;
use App\Services\PembatasanLV\PembatasanLVControlRoomContextService;
use App\Services\PembatasanLV\PembatasanLVDriverOptionService;
use App\Services\PembatasanLV\PembatasanLVShiftService;
use App\Services\PembatasanLV\PembatasanLVSiteLokasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PembatasanLVInputasiController extends Controller
{
    use ProvidesPembatasanLVLayout;

    public function __construct(
        private readonly PembatasanLVShiftService $shiftService,
        private readonly PembatasanLVControlRoomContextService $controlRoomContext,
        private readonly PembatasanLVSiteLokasiService $siteLokasiService,
        private readonly PembatasanLVDriverOptionService $driverOptionService,
        private readonly PembatasanLVKapasitasLokasiService $kapasitasLokasiService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $now = now()->timezone(config('app.timezone'));
        $controlRooms = $this->controlRoomContext->controlRoomsForUser($user);

        return view('PembatasanLV.inputasi.index', [
            'navActive' => 'inputasi',
            'navItems' => $this->pembatasanLvNavItems(),
            'sites' => CctvData::query()
                ->whereNotNull('site')
                ->where('site', '!=', '')
                ->distinct()
                ->orderBy('site')
                ->pluck('site'),
            'controlRooms' => CctvData::query()
                ->whereNotNull('control_room')
                ->where('control_room', '!=', '')
                ->distinct()
                ->orderBy('control_room')
                ->pluck('control_room'),
            'formContext' => [
                'shift' => $this->shiftService->resolveShift($now),
                'shift_label' => $this->shiftService->shiftLabel($this->shiftService->resolveShift($now)),
                'checkin_at' => $now->format('Y-m-d\TH:i'),
                'checkin_display' => $now->format('d M Y H:i'),
                'creator_name' => (string) ($user?->name ?? '—'),
                'creator_id' => $user?->id,
                'control_room' => $controlRooms->first(),
                'control_rooms' => $controlRooms->all(),
                'timezone' => config('app.timezone'),
            ],
            'aktivitasOptions' => MasterAktivitas::query()
                ->orderBy('nama_aktivitas')
                ->pluck('nama_aktivitas'),
        ]);
    }

    public function storeLv(PembatasanLVInputasiLvRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $now = now()->timezone(config('app.timezone'));
        $controlRoom = trim((string) $request->validated('control_room'));

        if ($controlRoom === '') {
            return back()
                ->withInput()
                ->withErrors(['control_room' => 'Control room tidak ditemukan untuk akun Anda.']);
        }

        PembatasanLvInputasi::query()->create([
            'shift' => $this->shiftService->resolveShift($now),
            'status' => $request->validated('status'),
            'nama_driver' => $request->validated('nama_driver'),
            'driver_ref' => $request->validated('driver_ref'),
            'no_lambung' => $request->validated('no_lambung'),
            'id_unit' => $request->validated('id_unit'),
            'lokasi' => $request->validated('lokasi'),
            'detail_lokasi' => $request->validated('detail_lokasi'),
            'creator_id' => $user?->id,
            'creator_name' => (string) ($user?->name ?? '—'),
            'control_room' => $controlRoom,
            'aktivitas' => $request->validated('aktivitas'),
            'checkin_at' => $now,
            'catatan' => $request->validated('catatan'),
        ]);

        return redirect()
            ->route('pembatasan-lv.inputasi.index', ['tab' => 'lv'])
            ->with('success', 'Inputasi LV berhasil disimpan.');
    }

    public function storeOrang(PembatasanLVInputasiOrangRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $now = now()->timezone(config('app.timezone'));
        $controlRoom = trim((string) $request->validated('control_room'));

        if ($controlRoom === '') {
            return back()
                ->withInput()
                ->withErrors(['control_room' => 'Control room tidak ditemukan untuk akun Anda.']);
        }

        PembatasanOrangInputasi::query()->create([
            'shift' => $this->shiftService->resolveShift($now),
            'status' => $request->validated('status'),
            'sid' => $request->validated('sid'),
            'nama' => $request->validated('nama'),
            'nik' => $request->validated('nik'),
            'nama_perusahaan' => $request->validated('nama_perusahaan'),
            'site' => $request->validated('site'),
            'lokasi' => $request->validated('lokasi'),
            'detail_lokasi' => $request->validated('detail_lokasi'),
            'creator_id' => $user?->id,
            'creator_name' => (string) ($user?->name ?? '—'),
            'control_room' => $controlRoom,
            'aktivitas' => $request->validated('aktivitas'),
            'checkin_at' => $now,
            'catatan' => $request->validated('catatan'),
        ]);

        return redirect()
            ->route('pembatasan-lv.inputasi.index', ['tab' => 'orang'])
            ->with('success', 'Inputasi orang berhasil disimpan.');
    }

    public function optionsUnits(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $limit = min(max((int) $request->query('limit', 30), 5), 100);

        $query = BecomelineUnit::query()
            ->select(['id_unit', 'no_lambung', 'sid_unit', 'perusahaan', 'jenis_unit'])
            ->whereNotNull('no_lambung')
            ->where('no_lambung', '!=', '')
            ->orderBy('no_lambung');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('no_lambung', 'like', '%'.$q.'%')
                    ->orWhere('sid_unit', 'like', '%'.$q.'%')
                    ->orWhere('perusahaan', 'like', '%'.$q.'%');
            });
        }

        $data = $query->limit($limit)->get()->map(fn (BecomelineUnit $row) => [
            'id_unit' => (string) $row->id_unit,
            'no_lambung' => (string) $row->no_lambung,
            'label' => trim((string) $row->no_lambung),
            'subtitle' => trim(collect([
                $row->sid_unit,
                $row->perusahaan,
                $row->jenis_unit,
            ])->filter()->implode(' • ')),
        ])->values();

        return response()->json(['data' => $data]);
    }

    public function optionsDrivers(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $limit = min(max((int) $request->query('limit', 30), 5), 100);

        $data = $this->driverOptionService->options($q, $limit)->map(fn (array $row) => [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'label' => $row['nama'],
            'subtitle' => trim(collect([
                $row['kode_sid'] ?? null,
                $row['nik'] ?? null,
                $row['nama_perusahaan'] ?? null,
                $row['site'] ?? null,
            ])->filter()->implode(' • ')),
        ])->values();

        return response()->json(['data' => $data]);
    }

    public function optionsSid(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $limit = min(max((int) $request->query('limit', 30), 5), 100);

        $data = $this->driverOptionService->options($q, $limit)->map(fn (array $row) => [
            'id' => $row['kode_sid'],
            'sid' => $row['kode_sid'],
            'label' => $row['kode_sid'],
            'nama' => $row['nama'],
            'nik' => $row['nik'],
            'nama_perusahaan' => $row['nama_perusahaan'],
            'site' => $row['site'],
            'subtitle' => trim(collect([
                $row['nama'],
                $row['nama_perusahaan'],
                $row['site'],
            ])->filter()->implode(' • ')),
        ])->values();

        return response()->json(['data' => $data]);
    }

    public function optionsLokasi(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $limit = min(max((int) $request->query('limit', 50), 5), 100);

        $data = $this->siteLokasiService->lokasiOptions($q, $limit)->map(fn (string $value) => [
            'value' => $value,
            'label' => $value,
        ])->values();

        return response()->json(['data' => $data]);
    }

    public function optionsDetailLokasi(Request $request): JsonResponse
    {
        $lokasi = trim((string) $request->query('lokasi', ''));
        $q = trim((string) $request->query('q', ''));
        $limit = min(max((int) $request->query('limit', 50), 5), 100);

        $data = $this->siteLokasiService->detailLokasiOptions($lokasi, $q, $limit)->map(fn (string $value) => [
            'value' => $value,
            'label' => $value,
        ])->values();

        return response()->json(['data' => $data]);
    }

    public function optionsAktivitas(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = MasterAktivitas::query()->orderBy('nama_aktivitas');
        if ($q !== '') {
            $query->where('nama_aktivitas', 'like', '%'.$q.'%');
        }

        $data = $query->limit(50)->pluck('nama_aktivitas')->map(fn (string $value) => [
            'value' => $value,
            'label' => $value,
        ])->values();

        return response()->json(['data' => $data]);
    }

    public function kapasitasLokasi(Request $request): JsonResponse
    {
        $lokasi = trim((string) $request->query('lokasi', ''));
        $detailLokasi = trim((string) $request->query('detail_lokasi', ''));

        if ($lokasi === '') {
            return response()->json([
                'data' => [
                    'has_batas' => false,
                    'can_input' => true,
                    'batas_lv' => null,
                    'terpakai' => 0,
                    'tersisa' => null,
                    'message' => null,
                ],
            ]);
        }

        return response()->json([
            'data' => $this->kapasitasLokasiService->check(
                $lokasi,
                $detailLokasi !== '' ? $detailLokasi : null
            ),
        ]);
    }
}
