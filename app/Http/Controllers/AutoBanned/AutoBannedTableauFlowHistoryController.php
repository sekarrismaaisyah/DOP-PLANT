<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Services\AutoBanned\AutoBannedTableauFlowHistoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedTableauFlowHistoryController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function __construct(
        private readonly AutoBannedTableauFlowHistoryService $tableauFlowHistoryService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->tableauFlowHistoryService->resolveFilters($request);

        return view('AutoBanned.tableau-flow-history.index', [
            'navActive' => 'tableau-flow-history',
            'navItems' => $this->autoBannedNavItems(),
            'filters' => $filters,
            'filterRoute' => 'auto-banned.tableau-flow-history.index',
            'tableAvailable' => $this->tableauFlowHistoryService->tableAvailable(),
            'historySummary' => $this->tableauFlowHistoryService->summary($filters),
            'historyRows' => $this->tableauFlowHistoryService->paginatedHistory($filters),
            'statusCodeOptions' => $this->tableauFlowHistoryService->statusCodeOptions(),
            'flowNameOptions' => $this->tableauFlowHistoryService->flowNameOptions(),
        ]);
    }
}
