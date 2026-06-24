<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Support\DopSafety\DopSafetyProgramDefinition;
use Illuminate\Http\Request;

class DopSafetyDashboardService
{
    /**
     * @return array{
     *     filters: array{date: string, shift: string, section: string},
     *     filter_options: array{shifts: list<string>, sections: list<string>},
     *     summary: array{total_activities: int, approved_dop: int, oji_approved: int, coverage_pct: float},
     *     kpi_levels: list<array<string, mixed>>,
     *     flow_steps: list<array<string, mixed>>,
     *     leading_indicator: array{label: string, target_met: bool, message: string}
     * }
     */
    public function buildDashboard(Request $request): array
    {
        $date = $request->filled('date')
            ? (string) $request->get('date')
            : now()->toDateString();

        $shift = $request->filled('shift')
            ? (string) $request->get('shift')
            : 'Shift 1';

        $section = $request->filled('section')
            ? (string) $request->get('section')
            : 'Semua Section';

        $kpiLevels = DopSafetyProgramDefinition::kpiLevels();

        foreach ($kpiLevels as &$level) {
            $level['progress'] = $this->demoProgressForLevel($level['level']);
        }
        unset($level);

        return [
            'filters' => [
                'date' => $date,
                'shift' => $shift,
                'section' => $section,
            ],
            'filter_options' => [
                'shifts' => ['Shift 1', 'Shift 2'],
                'sections' => ['Semua Section', 'Wheel', 'Track', 'SPEX', 'Tyre'],
            ],
            'summary' => [
                'total_activities' => 24,
                'approved_dop' => 22,
                'oji_approved' => 24,
                'coverage_pct' => 91.7,
            ],
            'kpi_levels' => $kpiLevels,
            'flow_steps' => DopSafetyProgramDefinition::l1L2FlowSteps(),
            'leading_indicator' => [
                'label' => 'Daily Coverage Area Based on DOP',
                'target_met' => true,
                'message' => 'Target SAP dan Daily Coverage tercapai — pengawas diperbolehkan bekerja.',
            ],
        ];
    }

    private function demoProgressForLevel(string $level): int
    {
        return match ($level) {
            'L1 & L2' => 94,
            'L3' => 88,
            'L4' => 100,
            default => 0,
        };
    }
}
