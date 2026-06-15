<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use Illuminate\View\View;

class AutoBannedInputasiController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function index(): View
    {
        return view('AutoBanned.inputasi.index', [
            'navActive' => 'inputasi',
            'navItems' => $this->autoBannedNavItems(),
        ]);
    }
}
