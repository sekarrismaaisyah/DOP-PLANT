<?php

declare(strict_types=1);

namespace App\Http\Controllers\FatigueManagement\Concerns;

trait ProvidesFatigueManagementLayout
{
    /**
     * @return list<array{key: string, label: string, route: string}>
     */
    protected function fatigueManagementNavItems(): array
    {
        return [
            [
                'key' => 'monitoring',
                'label' => 'Monitoring Program',
                'route' => 'fatigue-management.dashboard',
            ],
        ];
    }
}
