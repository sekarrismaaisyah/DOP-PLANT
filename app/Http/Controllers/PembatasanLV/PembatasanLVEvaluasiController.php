<?php

declare(strict_types=1);

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PembatasanLV\Concerns\ProvidesPembatasanLVLayout;
use App\Services\PembatasanLV\PembatasanLVEvaluasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembatasanLVEvaluasiController extends Controller
{
    use ProvidesPembatasanLVLayout;

    public function __construct(
        private readonly PembatasanLVEvaluasiService $evaluasiService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'site' => trim((string) $request->query('site', 'GMO')),
            'tanggal' => $request->query('tanggal', now()->toDateString()),
            'shift' => trim((string) $request->query('shift', '')),
            'perusahaan' => trim((string) $request->query('perusahaan', '')),
        ];

        $dashboard = $this->evaluasiService->buildDashboard($filters);

        return view('PembatasanLV.evaluasi.index', [
            'navActive' => 'evaluasi',
            'navItems' => $this->pembatasanLvNavItems(),
            'filters' => $dashboard['filters'],
            'summary' => $dashboard['summary'],
            'rows' => $dashboard['rows'],
            'logbookRows' => $dashboard['logbook_rows'],
            'sapRows' => $dashboard['sap_rows'],
            'filterOptions' => $dashboard['filter_options'],
            'sapAvailable' => $dashboard['sap_available'],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $filters = [
            'site' => trim((string) $request->query('site', 'GMO')),
            'tanggal' => $request->query('tanggal', now()->toDateString()),
            'shift' => trim((string) $request->query('shift', '')),
            'perusahaan' => trim((string) $request->query('perusahaan', '')),
        ];

        $dashboard = $this->evaluasiService->buildDashboard($filters);

        return response()->json([
            'summary' => $dashboard['summary'],
            'rows' => $dashboard['rows'],
            'logbook_rows' => $dashboard['logbook_rows'],
            'sap_rows' => $dashboard['sap_rows'],
            'filters' => $dashboard['filters'],
            'sap_available' => $dashboard['sap_available'],
        ]);
    }
}
