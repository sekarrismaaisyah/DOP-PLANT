<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Models\RosterPlanning;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Performance Dashboard Sistem Roster.
     */
    public function index(): View
    {
        $assignedPlannings = RosterPlanning::with('karyawans')
            ->where('status', 'assigned')
            ->orderByDesc('tanggal')
            ->orderByDesc('updated_at')
            ->get();

        return view('SistemRoster.dashboard.index', [
            'assignedPlannings' => $assignedPlannings,
        ]);
    }
}
