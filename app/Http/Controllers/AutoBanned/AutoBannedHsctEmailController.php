<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Models\AutoBannedHsctCampaignItem;
use App\Services\AutoBanned\AutoBannedHsctEmailService;
use App\Services\AutoBanned\AutoBannedOverviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedHsctEmailController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function __construct(
        private readonly AutoBannedOverviewService $overviewService,
        private readonly AutoBannedHsctEmailService $hsctEmailService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->overviewService->resolveFilters($request);
        $period = $this->overviewService->resolvePeriod($filters);
        $resolvedFilters = array_merge($filters, [
            'week' => $period['week'],
            'year' => $period['year'],
        ]);
        $filterOptions = $this->overviewService->filterOptions($resolvedFilters);

        $week = $resolvedFilters['week'];
        $year = $resolvedFilters['year'];

        return view('AutoBanned.hsct-email.index', [
            'navActive' => 'hsct-email',
            'navItems' => $this->autoBannedNavItems(),
            'filters' => $resolvedFilters,
            'period' => $period,
            'filterOptions' => $filterOptions,
            'filterRoute' => 'auto-banned.hsct-email.index',
            'hsctCampaign' => $this->hsctEmailService->activeCampaignSummary($week, $year),
            'hsctEmailHistory' => $this->hsctEmailService->emailHistory($week, $year, 50),
            'hsctHistorySummary' => $this->hsctEmailService->emailHistorySummary($week, $year),
            'hsctEmailAvailable' => $this->hsctEmailService->tablesAvailable(),
            'hsctPendingItems' => $this->hsctEmailService->pendingCampaignItems($week, $year),
        ]);
    }

    public function sendInitial(Request $request): RedirectResponse
    {
        $period = $this->hsctEmailService->resolvePeriod(
            $request->input('week'),
            $request->input('year'),
        );

        $result = $this->hsctEmailService->sendInitialEmail(
            $period['week'],
            $period['year'],
            (bool) $request->boolean('force'),
        );

        return $this->redirectWithEmailResult($request, $result);
    }

    public function sendReminder(Request $request): RedirectResponse
    {
        $period = $this->hsctEmailService->resolvePeriod(
            $request->input('week'),
            $request->input('year'),
        );

        $campaign = $this->hsctEmailService->findCampaign($period['week'], $period['year']);
        if ($campaign === null) {
            return redirect()
                ->route('auto-banned.hsct-email.index', $request->only(['site', 'week', 'year', 'perusahaan', 'q']))
                ->with('error', 'Campaign belum ada. Kirim email awal terlebih dahulu.');
        }

        $result = $this->hsctEmailService->sendReminderEmail($campaign, (bool) $request->boolean('force'));

        return $this->redirectWithEmailResult($request, $result);
    }

    public function confirmItem(Request $request, AutoBannedHsctCampaignItem $item): RedirectResponse
    {
        $this->hsctEmailService->confirmCampaignItem($item);

        return redirect()
            ->route('auto-banned.hsct-email.index', $request->only(['site', 'week', 'year', 'perusahaan', 'q']))
            ->with('success', 'SID '.$item->sid.' ditandai sudah banned oleh HSECT.');
    }

    /**
     * @param  array{action: string, message: string, sent: bool}  $result
     */
    private function redirectWithEmailResult(Request $request, array $result): RedirectResponse
    {
        $key = in_array($result['action'], ['error'], true) || ($result['action'] === 'initial' && ! $result['sent'])
            ? 'error'
            : 'success';

        return redirect()
            ->route('auto-banned.hsct-email.index', $request->only(['site', 'week', 'year', 'perusahaan', 'q']))
            ->with($key, $result['message']);
    }
}
