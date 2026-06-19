<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Http\Requests\AutoBanned\AutoBannedReviewUnbanRequestRequest;
use App\Models\AutoBannedUnbanRequest;
use App\Services\AutoBanned\AutoBannedOverviewService;
use App\Services\AutoBanned\AutoBannedSodVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedSodVerificationController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function __construct(
        private readonly AutoBannedOverviewService $overviewService,
        private readonly AutoBannedSodVerificationService $sodVerificationService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->overviewService->resolveFilters($request);
        $period = $this->overviewService->resolvePeriod($filters);
        $resolvedFilters = array_merge($filters, [
            'week' => $period['week'],
            'year' => $period['year'],
            'status' => trim((string) $request->query('status', 'pending')),
        ]);

        if ($resolvedFilters['status'] === '') {
            $resolvedFilters['status'] = 'pending';
        }

        $filterOptions = $this->overviewService->filterOptions($resolvedFilters);

        return view('AutoBanned.sod-verification.index', [
            'navActive' => 'sod-verification',
            'navItems' => $this->autoBannedNavItems(),
            'filters' => $resolvedFilters,
            'period' => $period,
            'filterOptions' => $filterOptions,
            'filterRoute' => 'auto-banned.sod-verification.index',
            'tableAvailable' => $this->sodVerificationService->tableAvailable(),
            'summaryCounts' => $this->sodVerificationService->summaryCounts($resolvedFilters),
            'submittedRequests' => $this->sodVerificationService->listSubmittedRequests($resolvedFilters),
        ]);
    }

    public function review(
        AutoBannedReviewUnbanRequestRequest $request,
        AutoBannedUnbanRequest $unbanRequest,
    ): RedirectResponse {
        $user = $request->user();
        $catatan = $request->input('catatan_review');

        if ($request->isApprove()) {
            $this->sodVerificationService->approve($unbanRequest, $user, is_string($catatan) ? $catatan : null);
            $message = 'Pengajuan SID '.$unbanRequest->sid.' disetujui.';
        } else {
            $this->sodVerificationService->reject($unbanRequest, $user, is_string($catatan) ? $catatan : null);
            $message = 'Pengajuan SID '.$unbanRequest->sid.' ditolak.';
        }

        return redirect()
            ->route('auto-banned.sod-verification.index', $request->only(['site', 'week', 'year', 'perusahaan', 'q', 'status']))
            ->with('success', $message);
    }
}
