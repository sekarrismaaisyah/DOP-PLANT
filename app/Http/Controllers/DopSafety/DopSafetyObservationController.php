<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Support\DopSafety\DopSafetyProgramDefinition;
use Illuminate\View\View;

class DopSafetyObservationController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function index(): View
    {
        return view('DopSafety.observation.index', $this->dopSafetyViewData('observation', [
            'checklist' => DopSafetyProgramDefinition::observationChecklist(),
            'coverageTargets' => DopSafetyProgramDefinition::observationCoverageTargets(),
            'observations' => $this->demoObservations(),
        ]));
    }

    /**
     * @return list<array{id: int, observer: string, role: string, activity: string, coverage_pct: float, beats_logged: bool}>
     */
    private function demoObservations(): array
    {
        return [
            [
                'id' => 1,
                'observer' => 'GL Wheel',
                'role' => 'GL',
                'activity' => 'Overhaul Final Drive Unit 793',
                'coverage_pct' => 100.0,
                'beats_logged' => true,
            ],
            [
                'id' => 2,
                'observer' => 'SH Track',
                'role' => 'SH',
                'activity' => 'Replace Track Roller',
                'coverage_pct' => 100.0,
                'beats_logged' => true,
            ],
            [
                'id' => 3,
                'observer' => 'Safety PAMA',
                'role' => 'SHE',
                'activity' => 'Tyre Change HD785',
                'coverage_pct' => 100.0,
                'beats_logged' => false,
            ],
        ];
    }
}
