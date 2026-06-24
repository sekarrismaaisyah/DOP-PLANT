<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use Illuminate\View\View;

class DopSafetyCoverageController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function index(): View
    {
        return view('DopSafety.coverage.index', $this->dopSafetyViewData('coverage', [
            'leadingIndicator' => [
                'title' => 'Leading Indicator — Daily Coverage',
                'concept' => 'Daily Coverage Area Based on Daily Operation Plan (DOP)',
                'rule' => 'Jika target SAP dan Daily Coverage tidak tercapai, pengawas TIDAK DIPERBOLEHKAN BEKERJA!',
            ],
            'areas' => $this->demoAreaCoverage(),
        ]));
    }

    /**
     * @return list<array{area: string, sap_target: float, coverage_pct: float, status: string, supervisor_allowed: bool}>
     */
    private function demoAreaCoverage(): array
    {
        return [
            ['area' => 'Workshop Wheel', 'sap_target' => 100.0, 'coverage_pct' => 100.0, 'status' => 'Tercapai', 'supervisor_allowed' => true],
            ['area' => 'Workshop Track', 'sap_target' => 100.0, 'coverage_pct' => 95.0, 'status' => 'Di bawah target', 'supervisor_allowed' => false],
            ['area' => 'Workshop SPEX', 'sap_target' => 100.0, 'coverage_pct' => 100.0, 'status' => 'Tercapai', 'supervisor_allowed' => true],
            ['area' => 'Workshop Tyre', 'sap_target' => 100.0, 'coverage_pct' => 88.0, 'status' => 'Di bawah target', 'supervisor_allowed' => false],
        ];
    }
}
