<?php

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PembatasanLV\Concerns\ProvidesPembatasanLVLayout;
use App\Models\CctvControlRoomPengawas;
use App\Models\CctvData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembatasanLVMasterDataController extends Controller
{
    use ProvidesPembatasanLVLayout;

    public function index(Request $request): View
    {
        $activeTab = in_array($request->query('tab'), ['control-room', 'lv'], true)
            ? $request->query('tab')
            : 'site';

        $sites = CctvData::query()
            ->whereNotNull('site')
            ->where('site', '!=', '')
            ->distinct()
            ->orderBy('site')
            ->pluck('site');

        $controlRooms = CctvData::query()
            ->select('site', 'control_room')
            ->whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->distinct()
            ->orderBy('site')
            ->orderBy('control_room')
            ->get();

        $controlRoomOptions = CctvData::query()
            ->whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->distinct()
            ->pluck('control_room')
            ->merge(
                CctvControlRoomPengawas::query()
                    ->whereNotNull('control_room')
                    ->where('control_room', '!=', '')
                    ->distinct()
                    ->pluck('control_room')
            )
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('PembatasanLV.master-data.index', [
            'navActive' => 'master-data',
            'navItems' => $this->pembatasanLvNavItems(),
            'activeTab' => $activeTab,
            'siteOptions' => $sites,
            'controlRooms' => $controlRooms,
            'controlRoomOptions' => $controlRoomOptions,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }
}
