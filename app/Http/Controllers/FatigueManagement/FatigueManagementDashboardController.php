<?php

declare(strict_types=1);

namespace App\Http\Controllers\FatigueManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FatigueManagement\Concerns\ProvidesFatigueManagementLayout;
use App\Services\FatigueManagement\FatigueManagementMonitoringService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FatigueManagementDashboardController extends Controller
{
    use ProvidesFatigueManagementLayout;

    public function index(Request $request, FatigueManagementMonitoringService $monitoringService): View
    {
        $dashboard = $monitoringService->buildDashboard(
            $request->filled('year') ? (int) $request->get('year') : null,
            $request->filled('iso_week') ? (string) $request->get('iso_week') : null,
            $request->filled('partner') ? (string) $request->get('partner') : null,
            $request->filled('program') ? (string) $request->get('program') : null,
            $request->filled('evidence_status') ? (string) $request->get('evidence_status') : null,
            $request->filled('evaluation_status') ? (string) $request->get('evaluation_status') : null,
        );

        return view('fatigue-management.dashboard', [
            'navActive' => 'monitoring',
            'navItems' => $this->fatigueManagementNavItems(),
            'programLabel' => 'Fatigue Management GMO',
            'dashboard' => $dashboard,
            'filters' => $dashboard['filters'] ?? [],
            'filterOptions' => $dashboard['filter_options'] ?? [],
            'summary' => $dashboard['summary'] ?? [],
            'rows' => $dashboard['rows'] ?? [],
            'chart' => $dashboard['chart'] ?? [],
        ]);
    }
}
