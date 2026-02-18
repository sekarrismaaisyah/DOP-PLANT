<?php

namespace App\Http\Controllers;

use App\Models\SupervisoryAlertLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupervisoryAlertLogController extends Controller
{
    /** Kolom untuk ordering DataTables */
    private const COLUMNS = ['tanggal', 'nama_lokasi', 'risk_level', 'has_sap_report', 'has_online_cctv', 'is_high_risk_area', 'updated_at'];

    /**
     * Halaman alert log dengan tab (tab pertama: Supervisory).
     */
    public function index()
    {
        return view('supervisory-alert-log.index');
    }

    /**
     * Data untuk DataTables (server-side).
     */
    public function getData(Request $request)
    {
        try {
            $draw = (int) $request->get('draw', 1);
            $start = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 25);
            $length = min(max($length, 1), 100);
            $searchValue = $request->get('search')['value'] ?? '';
            $order = $request->get('order', []);
            $orderColIndex = isset($order[0]['column']) ? (int) $order[0]['column'] : 0;
            $orderDir = isset($order[0]['dir']) && strtolower($order[0]['dir']) === 'asc' ? 'asc' : 'desc';

            $orderColumn = self::COLUMNS[$orderColIndex] ?? 'tanggal';

            $query = SupervisoryAlertLog::query();

            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            if ($request->filled('risk_level') && in_array($request->risk_level, [SupervisoryAlertLog::RISK_HIGH, SupervisoryAlertLog::RISK_MEDIUM], true)) {
                $query->where('risk_level', $request->risk_level);
            }
            if ($searchValue !== '') {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('nama_lokasi', 'like', '%' . $searchValue . '%')
                        ->orWhere('risk_level', 'like', '%' . $searchValue . '%')
                        ->orWhere('id_lokasi', 'like', '%' . $searchValue . '%');
                });
            }

            $recordsTotal = SupervisoryAlertLog::query()->count();
            $recordsFiltered = $query->count();

            $rows = $query->orderBy($orderColumn, $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            $data = $rows->map(function ($row) {
                $riskBadge = $row->risk_level === 'HIGH'
                    ? '<span class="badge bg-danger">HIGH</span>'
                    : ($row->risk_level === 'MEDIUM'
                        ? '<span class="badge bg-warning text-dark">MEDIUM</span>'
                        : '<span class="badge bg-secondary">' . e($row->risk_level) . '</span>');

                return [
                    'tanggal' => $row->tanggal ? $row->tanggal->format('d/m/Y') : '-',
                    'nama_lokasi' => $row->nama_lokasi ?? '-',
                    'risk_level' => $riskBadge,
                    'has_sap_report' => $row->has_sap_report ? '<span class="text-success">Ya</span>' : '<span class="text-muted">Tidak</span>',
                    'has_online_cctv' => $row->has_online_cctv ? '<span class="text-success">Ya</span>' : '<span class="text-muted">Tidak</span>',
                    'is_high_risk_area' => $row->is_high_risk_area ? '<span class="text-warning">Ya</span>' : '<span class="text-muted">Tidak</span>',
                    'updated_at' => $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : '-',
                ];
            })->toArray();

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('SupervisoryAlertLogController getData: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Gagal memuat data.',
            ], 500);
        }
    }
}
