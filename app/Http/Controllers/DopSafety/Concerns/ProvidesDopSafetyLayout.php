<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety\Concerns;

trait ProvidesDopSafetyLayout
{
    /**
     * @return list<array{key: string, label: string, route: string}>
     */
    protected function dopSafetyNavItems(): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Dashboard KPI', 'route' => 'dop-safety.dashboard'],
            ['key' => 'oji', 'label' => 'OJI', 'route' => 'dop-safety.oji.index'],
            ['key' => 'plan', 'label' => 'Pengajuan DOP', 'route' => 'dop-safety.plan.index'],
            ['key' => 'inspection', 'label' => 'Inspeksi L1–L3', 'route' => 'dop-safety.inspection.index'],
            ['key' => 'observation', 'label' => 'Observasi', 'route' => 'dop-safety.observation.index'],
            ['key' => 'review', 'label' => 'Review L4', 'route' => 'dop-safety.review.index'],
            ['key' => 'fgd', 'label' => 'FGD', 'route' => 'dop-safety.fgd.index'],
            ['key' => 'coverage', 'label' => 'Daily Coverage', 'route' => 'dop-safety.coverage.index'],
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    protected function dopSafetyViewData(string $navActive, array $extra = []): array
    {
        return array_merge([
            'navActive' => $navActive,
            'navItems' => $this->dopSafetyNavItems(),
            'programLabel' => config('dop_safety.program_title', 'Program Darurat Keselamatan Plant DOP'),
            'programCode' => config('dop_safety.program_code', 'PDKP-GMO-001'),
        ], $extra);
    }
}
