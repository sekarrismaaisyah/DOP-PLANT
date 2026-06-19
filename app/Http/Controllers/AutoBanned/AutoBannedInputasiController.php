<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Services\AutoBanned\AutoBannedOverviewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedInputasiController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function __construct(
        private readonly AutoBannedOverviewService $overviewService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->overviewService->resolveFilters($request);
        $period = $this->overviewService->resolvePeriod($filters);

        return view('AutoBanned.inputasi.index', [
            'navActive' => 'inputasi',
            'navItems' => $this->autoBannedNavItems(),
            'filters' => array_merge($filters, [
                'week' => $period['week'],
                'year' => $period['year'],
            ]),
            'period' => $period,
            'prefillSid' => strtoupper(trim((string) $request->query('sid', ''))),
        ]);
    }
}
