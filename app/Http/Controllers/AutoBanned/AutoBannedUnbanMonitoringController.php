<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Services\AutoBanned\AutoBannedUnbanMonitoringService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedUnbanMonitoringController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function __construct(
        private readonly AutoBannedUnbanMonitoringService $unbanMonitoringService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->unbanMonitoringService->resolveFilters($request);
        $dashboard = $this->unbanMonitoringService->buildDashboard($filters);

        return view('AutoBanned.unban-monitoring.index', [
            'navActive' => 'unban-monitoring',
            'navItems' => $this->autoBannedNavItems(),
            'filters' => $dashboard['filters'],
            'period' => $dashboard['period'],
            'filterOptions' => $dashboard['filterOptions'],
            'stats' => $dashboard['stats'],
            'chartData' => $dashboard['chartData'],
            'unbanRows' => $dashboard['unbanRows'],
            'tableAvailable' => $dashboard['tableAvailable'],
        ]);
    }
}
