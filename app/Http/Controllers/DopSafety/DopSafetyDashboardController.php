<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Services\DopSafety\DopSafetyDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DopSafetyDashboardController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function __construct(
        private readonly DopSafetyDashboardService $dashboardService,
    ) {}

    public function index(Request $request): View
    {
        $dashboard = $this->dashboardService->buildDashboard($request);

        return view('DopSafety.dashboard', $this->dopSafetyViewData('dashboard', [
            'dashboard' => $dashboard,
            'filters' => $dashboard['filters'],
            'filterOptions' => $dashboard['filter_options'],
            'summary' => $dashboard['summary'],
            'kpiLevels' => $dashboard['kpi_levels'],
            'flowSteps' => $dashboard['flow_steps'],
            'leadingIndicator' => $dashboard['leading_indicator'],
        ]));
    }
}
