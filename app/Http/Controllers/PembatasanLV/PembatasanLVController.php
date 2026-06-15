<?php

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PembatasanLV\Concerns\ProvidesPembatasanLVInputasiFormContext;
use App\Http\Controllers\PembatasanLV\Concerns\ProvidesPembatasanLVLayout;
use App\Models\CctvData;
use App\Models\PembatasanLvInputasi;
use App\Models\PembatasanOrangInputasi;
use App\Services\PembatasanLV\PembatasanLVControlRoomContextService;
use App\Services\PembatasanLV\PembatasanLVOverviewService;
use App\Services\PembatasanLV\PembatasanLVShiftService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PembatasanLVController extends Controller
{
    use ProvidesPembatasanLVLayout;
    use ProvidesPembatasanLVInputasiFormContext;

    public function __construct(
        private readonly PembatasanLVOverviewService $overviewService,
        private readonly PembatasanLVShiftService $shiftService,
        private readonly PembatasanLVControlRoomContextService $controlRoomContext,
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $filters = [
            'site' => trim((string) $request->query('site', '')),
            'tanggal' => $request->query('tanggal', now()->toDateString()),
            'control_room' => trim((string) $request->query('control_room', '')),
        ];

        $sites = CctvData::query()
            ->whereNotNull('site')
            ->where('site', '!=', '')
            ->distinct()
            ->orderBy('site')
            ->pluck('site');

        $supervisedRooms = $this->overviewService->supervisedRooms($user, $filters);

        $controlRoomsQuery = CctvData::query()
            ->whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->distinct()
            ->orderBy('control_room');

        if ($filters['site'] !== '') {
            $controlRoomsQuery->where('site', $filters['site']);
        }

        if ($supervisedRooms->isNotEmpty()) {
            $controlRoomsQuery->whereIn('control_room', $supervisedRooms->all());
        }

        $controlRooms = $controlRoomsQuery->pluck('control_room');

        $lvMasukAktif = (clone $this->overviewService->lvMasukAktifQuery($user, $filters))->count();
        $lvKeluar = (clone $this->overviewService->lvKeluarQuery($user, $filters))->count();
        $lvMasukAktifList = $this->overviewService
            ->lvMasukAktifQuery($user, $filters)
            ->limit(100)
            ->get();

        $lvKeluarList = $this->overviewService
            ->lvKeluarQuery($user, $filters)
            ->limit(100)
            ->get();

        $lvAllList = $this->overviewService
            ->lvAllListQuery($user, $filters)
            ->limit(200)
            ->get();

        $orangMasukAktif = (clone $this->overviewService->orangMasukAktifQuery($user, $filters))->count();
        $orangKeluar = (clone $this->overviewService->orangKeluarQuery($user, $filters))->count();
        $orangMasukAktifList = $this->overviewService
            ->orangMasukAktifQuery($user, $filters)
            ->limit(100)
            ->get();
        $orangKeluarList = $this->overviewService
            ->orangKeluarQuery($user, $filters)
            ->limit(100)
            ->get();
        $orangAllList = $this->overviewService
            ->orangAllListQuery($user, $filters)
            ->limit(200)
            ->get();

        return view('PembatasanLV.index', [
            'navActive' => 'overview',
            'navItems' => $this->pembatasanLvNavItems(),
            'filters' => $filters,
            'sites' => $sites,
            'controlRooms' => $controlRooms,
            'supervisedRooms' => $supervisedRooms,
            'lvMasukAktif' => $lvMasukAktif,
            'lvKeluar' => $lvKeluar,
            'lvMasukAktifList' => $lvMasukAktifList,
            'lvKeluarList' => $lvKeluarList,
            'lvAllList' => $lvAllList,
            'orangMasukAktif' => $orangMasukAktif,
            'orangKeluar' => $orangKeluar,
            'orangMasukAktifList' => $orangMasukAktifList,
            'orangKeluarList' => $orangKeluarList,
            'orangAllList' => $orangAllList,
            'formContext' => $this->pembatasanLvInputasiFormContext($this->shiftService, $this->controlRoomContext, $user),
            'aktivitasOptions' => $this->pembatasanLvAktivitasOptions(),
        ]);
    }

    public function checkoutLv(Request $request, PembatasanLvInputasi $inputasi): RedirectResponse
    {
        $user = Auth::user();

        if (! $this->overviewService->userCanManageRecord($user, $inputasi)) {
            abort(403, 'Anda tidak berwenang checkout LV di control room ini.');
        }

        if ($inputasi->checkout_at !== null) {
            return back()->with('error', 'LV ini sudah di-checkout sebelumnya.');
        }

        $now = now()->timezone(config('app.timezone'));

        $inputasi->update([
            'checkout_at' => $now,
            'checkout_by_id' => $user?->id,
            'checkout_by_name' => (string) ($user?->name ?? '—'),
        ]);

        return back()->with('success', 'Checkout LV '.$inputasi->no_lambung.' berhasil pada '.$now->format('d M Y H:i').'.');
    }

    public function checkoutOrang(Request $request, PembatasanOrangInputasi $inputasi): RedirectResponse
    {
        $user = Auth::user();

        if (! $this->overviewService->userCanManageOrangRecord($user, $inputasi)) {
            abort(403, 'Anda tidak berwenang checkout orang di control room ini.');
        }

        if ($inputasi->checkout_at !== null) {
            return back()->with('error', 'Orang ini sudah di-checkout sebelumnya.');
        }

        $now = now()->timezone(config('app.timezone'));

        $inputasi->update([
            'checkout_at' => $now,
            'checkout_by_id' => $user?->id,
            'checkout_by_name' => (string) ($user?->name ?? '—'),
        ]);

        return back()->with('success', 'Checkout '.$inputasi->nama.' (SID: '.$inputasi->sid.') berhasil pada '.$now->format('d M Y H:i').'.');
    }

    public function lvMasukAktifData(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $filters = [
            'site' => trim((string) $request->query('site', '')),
            'tanggal' => $request->query('tanggal', now()->toDateString()),
            'control_room' => trim((string) $request->query('control_room', '')),
        ];

        $now = now()->timezone(config('app.timezone'));

        $rows = $this->overviewService
            ->lvMasukAktifQuery($user, $filters)
            ->limit(100)
            ->get()
            ->map(fn (PembatasanLvInputasi $row) => [
                'id' => $row->id,
                'nama_driver' => $row->nama_driver,
                'no_lambung' => $row->no_lambung,
                'checkin_at' => $row->checkin_at?->timezone(config('app.timezone'))->toIso8601String(),
                'lokasi' => $row->lokasi,
                'detail_lokasi' => $row->detail_lokasi,
                'durasi_detik' => $row->checkin_at
                    ? (int) $row->checkin_at->timezone(config('app.timezone'))->diffInSeconds($now)
                    : 0,
            ])
            ->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total' => $rows->count(),
                'server_now' => $now->toIso8601String(),
            ],
        ]);
    }

    public function orangMasukAktifData(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $filters = [
            'site' => trim((string) $request->query('site', '')),
            'tanggal' => $request->query('tanggal', now()->toDateString()),
            'control_room' => trim((string) $request->query('control_room', '')),
        ];

        $now = now()->timezone(config('app.timezone'));

        $rows = $this->overviewService
            ->orangMasukAktifQuery($user, $filters)
            ->limit(100)
            ->get()
            ->map(fn (PembatasanOrangInputasi $row) => [
                'id' => $row->id,
                'sid' => $row->sid,
                'nama' => $row->nama,
                'nama_perusahaan' => $row->nama_perusahaan,
                'checkin_at' => $row->checkin_at?->timezone(config('app.timezone'))->toIso8601String(),
                'lokasi' => $row->lokasi,
                'detail_lokasi' => $row->detail_lokasi,
                'durasi_detik' => $row->checkin_at
                    ? (int) $row->checkin_at->timezone(config('app.timezone'))->diffInSeconds($now)
                    : 0,
            ])
            ->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total' => $rows->count(),
                'server_now' => $now->toIso8601String(),
            ],
        ]);
    }
}
