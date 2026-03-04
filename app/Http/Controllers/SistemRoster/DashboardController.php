<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Performance Dashboard Sistem Roster.
     */
    public function index(): View
    {
        return view('SistemRoster.dashboard.index');
    }
}
