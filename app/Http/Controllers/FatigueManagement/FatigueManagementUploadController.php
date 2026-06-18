<?php

declare(strict_types=1);

namespace App\Http\Controllers\FatigueManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FatigueManagement\Concerns\ProvidesFatigueManagementLayout;
use App\Http\Controllers\FatigueManagement\Concerns\ResolvesFatigueManagementPartnerAccess;
use App\Services\FatigueManagement\FatigueManagementMonitoringService;
use App\Support\FatigueManagement\FatigueManagementFrequencyPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FatigueManagementUploadController extends Controller
{
    use ProvidesFatigueManagementLayout;
    use ResolvesFatigueManagementPartnerAccess;

    public function index(Request $request, FatigueManagementMonitoringService $monitoringService): View
    {
        $access = $this->fatiguePartnerAccess($request);
        $partnerFilter = $access->resolvePartnerFilter(
            $request->filled('partner') ? (string) $request->get('partner') : null,
        );

        $dashboard = $monitoringService->buildDashboard(
            $request->filled('year') ? (int) $request->get('year') : null,
            $request->filled('iso_week') ? (string) $request->get('iso_week') : null,
            $partnerFilter,
            $request->filled('program') ? (string) $request->get('program') : null,
            $request->filled('program_type') ? (string) $request->get('program_type') : null,
        );

        return view('fatigue-management.upload', [
            'navActive' => 'upload',
            'navItems' => $this->fatigueManagementNavItems($access),
            'programLabel' => 'Fatigue Management GMO',
            'dashboard' => $dashboard,
            'filters' => $dashboard['filters'] ?? [],
            'filterOptions' => $dashboard['filter_options'] ?? [],
            'rows' => $dashboard['rows'] ?? [],
            'uploadFrequencyGroups' => $dashboard['upload_frequency_groups'] ?? [],
            'uploadPageContext' => FatigueManagementFrequencyPlan::uploadPageContext(
                (int) ($dashboard['filters']['year'] ?? date('Y')),
                (string) ($dashboard['filters']['isoWeek'] ?? ''),
            ),
            'partnerAccess' => $this->fatiguePartnerAccessViewData($access),
        ]);
    }

    public function tutorial(Request $request): View
    {
        $access = $this->fatiguePartnerAccess($request);

        return view('fatigue-management.upload-tutorial-full', [
            'navActive' => 'upload',
            'navItems' => $this->fatigueManagementNavItems($access),
            'programLabel' => 'Fatigue Management GMO',
            'filters' => [
                'year' => $request->filled('year') ? (int) $request->get('year') : (int) date('Y'),
                'isoWeek' => $request->filled('iso_week')
                    ? (string) $request->get('iso_week')
                    : 'W' . str_pad((string) date('W'), 2, '0', STR_PAD_LEFT),
            ],
            'uploadPageContext' => FatigueManagementFrequencyPlan::uploadPageContext(
                $request->filled('year') ? (int) $request->get('year') : (int) date('Y'),
                $request->filled('iso_week')
                    ? (string) $request->get('iso_week')
                    : 'W' . str_pad((string) date('W'), 2, '0', STR_PAD_LEFT),
            ),
            'partnerAccess' => $this->fatiguePartnerAccessViewData($access),
        ]);
    }
}
