<?php

declare(strict_types=1);

namespace App\Http\Controllers\FatigueManagement\Concerns;

use App\Support\FatigueManagement\FatigueManagementPartnerAccessContext;

trait ProvidesFatigueManagementLayout
{
    /**
     * @return list<array{key: string, label: string, route: string}>
     */
    protected function fatigueManagementNavItems(?FatigueManagementPartnerAccessContext $access = null): array
    {
        $items = [];

        if ($access === null || $access->isGmoViewer || ! $access->isLocked()) {
            $items[] = [
                'key' => 'dashboard',
                'label' => 'Dashboard Checklist',
                'route' => 'fatigue-management.dashboard',
            ];
        }

        $items[] = [
            'key' => 'upload',
            'label' => 'Upload Evidence',
            'route' => 'fatigue-management.upload',
        ];

        return $items;
    }
}
