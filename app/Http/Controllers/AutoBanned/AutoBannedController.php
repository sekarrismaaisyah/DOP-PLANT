<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Models\AutoBannedHsctCampaign;
use App\Models\AutoBannedStatusSnapshot;
use App\Services\AutoBanned\AutoBannedHsctEmailService;
use App\Services\AutoBanned\AutoBannedMonitoringDummyService;
use App\Services\AutoBanned\AutoBannedOverviewService;
use App\Services\AutoBanned\AutoBannedScrapPollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function __construct(
        private readonly AutoBannedOverviewService $overviewService,
        private readonly AutoBannedScrapPollService $pollService,
        private readonly AutoBannedMonitoringDummyService $monitoringDummyService,
        private readonly AutoBannedHsctEmailService $hsctEmailService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->overviewService->resolveFilters($request);
        $overview = $this->overviewService->buildOverview($filters);

        return view('AutoBanned.index', [
            'navActive' => 'overview',
            'navItems' => $this->autoBannedNavItems(),
            'filters' => $overview['filters'],
            'period' => $overview['period'],
            'filterOptions' => $overview['filterOptions'],
            'stats' => $overview['stats'],
            'bannedRows' => $overview['bannedRows'],
            'monitoringLifecycleRows' => $this->monitoringDummyService->lifecycleRows(),
            'unbanRows' => $overview['unbanRows'],
            'syncStats' => $overview['syncStats'],
            'recentChanges' => $overview['recentChanges'],
            'pollMeta' => $overview['pollMeta'],
            'tableAvailable' => $overview['tableAvailable'],
            'trackingAvailable' => $overview['trackingAvailable'],
        ]);
    }

    public function markHsctSent(Request $request, AutoBannedStatusSnapshot $snapshot): RedirectResponse
    {
        $this->pollService->markHsctSent($snapshot);
        $this->syncCampaignForSnapshot($snapshot);

        return redirect()
            ->route('auto-banned.index', $request->only(['site', 'week', 'year', 'perusahaan', 'q']))
            ->with('success', 'SID '.$snapshot->sid.' ditandai terkirim ke HSECT.');
    }

    public function markHsctConfirmed(Request $request, AutoBannedStatusSnapshot $snapshot): RedirectResponse
    {
        $this->pollService->markHsctConfirmed($snapshot);
        $this->syncCampaignForSnapshot($snapshot);

        return redirect()
            ->route('auto-banned.index', $request->only(['site', 'week', 'year', 'perusahaan', 'q']))
            ->with('success', 'SID '.$snapshot->sid.' dikonfirmasi banned oleh HSECT.');
    }

    private function syncCampaignForSnapshot(AutoBannedStatusSnapshot $snapshot): void
    {
        $campaign = $this->hsctEmailService->findCampaign($snapshot->week, $snapshot->iso_year);
        if ($campaign instanceof AutoBannedHsctCampaign) {
            $this->hsctEmailService->syncCampaignConfirmation($campaign);
        }
    }
}
