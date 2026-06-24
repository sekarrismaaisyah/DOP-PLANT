<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Support\DopSafety\DopSafetyProgramDefinition;
use Illuminate\View\View;

class DopSafetyReviewController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function index(): View
    {
        return view('DopSafety.review.index', $this->dopSafetyViewData('review', [
            'duties' => DopSafetyProgramDefinition::l4ReviewDuties(),
            'kpi' => collect(DopSafetyProgramDefinition::kpiLevels())
                ->firstWhere('level', 'L4'),
            'reviews' => $this->demoReviews(),
        ]));
    }

    /**
     * @return list<array{id: int, activity: string, reviewer: string, decision: string, intervention: string, reviewed_at: string}>
     */
    private function demoReviews(): array
    {
        return [
            [
                'id' => 1,
                'activity' => 'Hot Work — Welding Frame 793',
                'reviewer' => 'DH Plant',
                'decision' => 'Disetujui dengan pengawasan tambahan',
                'intervention' => 'Safety standby selama pekerjaan',
                'reviewed_at' => now()->format('d M Y H:i'),
            ],
            [
                'id' => 2,
                'activity' => 'LOTO Breaker Panel SPEX',
                'reviewer' => 'PJO',
                'decision' => 'Perlu perbaikan JSA',
                'intervention' => 'Hold pekerjaan hingga JSA diperbarui',
                'reviewed_at' => now()->subHours(3)->format('d M Y H:i'),
            ],
        ];
    }
}
