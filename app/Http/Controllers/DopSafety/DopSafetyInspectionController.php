<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Support\DopSafety\DopSafetyProgramDefinition;
use Illuminate\View\View;

class DopSafetyInspectionController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function index(): View
    {
        return view('DopSafety.inspection.index', $this->dopSafetyViewData('inspection', [
            'l3Matrix' => DopSafetyProgramDefinition::l3InspectionMatrix(),
            'checklists' => DopSafetyProgramDefinition::inspectionChecklists(),
            'l1L2Flow' => DopSafetyProgramDefinition::l1L2FlowSteps(),
            'activities' => $this->demoInspectionActivities(),
        ]));
    }

    /**
     * @return list<array{id: int, activity: string, section: string, pre: string, during: string, post: string, l3: string}>
     */
    private function demoInspectionActivities(): array
    {
        return [
            [
                'id' => 1,
                'activity' => 'Overhaul Final Drive Unit 793',
                'section' => 'Wheel',
                'pre' => 'Selesai',
                'during' => 'Berlangsung',
                'post' => 'Belum',
                'l3' => 'Safety PAMA — Verified',
            ],
            [
                'id' => 2,
                'activity' => 'Replace Track Roller',
                'section' => 'Track',
                'pre' => 'Selesai',
                'during' => 'Selesai',
                'post' => 'Selesai',
                'l3' => 'Safety BC — Verified',
            ],
        ];
    }
}
