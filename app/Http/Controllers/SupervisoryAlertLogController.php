<?php

namespace App\Http\Controllers;

use App\Models\SupervisoryAlertLog;
use Illuminate\Http\Request;

class SupervisoryAlertLogController extends Controller
{
    /**
     * Halaman alert log dengan tab (tab pertama: Supervisory).
     */
    public function index(Request $request)
    {
        $query = SupervisoryAlertLog::query()
            ->orderBy('tanggal', 'desc')
            ->orderBy('nama_lokasi');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('risk_level') && in_array($request->risk_level, [SupervisoryAlertLog::RISK_HIGH, SupervisoryAlertLog::RISK_MEDIUM], true)) {
            $query->where('risk_level', $request->risk_level);
        }
        if ($request->filled('nama_lokasi')) {
            $query->where('nama_lokasi', 'like', '%' . $request->nama_lokasi . '%');
        }

        $alerts = $query->paginate(20)->withQueryString();

        return view('supervisory-alert-log.index', compact('alerts'));
    }
}
