<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedMasterDataController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function index(Request $request): View
    {
        $activeTab = in_array($request->query('tab'), ['control-room', 'lv'], true)
            ? $request->query('tab')
            : 'site';

        return view('AutoBanned.master-data.index', [
            'navActive' => 'master-data',
            'navItems' => $this->autoBannedNavItems(),
            'activeTab' => $activeTab,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }
}
