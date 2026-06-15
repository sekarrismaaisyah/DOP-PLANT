<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned\Concerns;

trait ProvidesAutoBannedLayout
{
    /**
     * @return array<int, array{key: string, label: string, route: string}>
     */
    protected function autoBannedNavItems(): array
    {
        return [
            [
                'key' => 'overview',
                'label' => 'Overview',
                'route' => 'auto-banned.index',
            ],
            [
                'key' => 'hsct-email',
                'label' => 'Riwayat Email HSECT',
                'route' => 'auto-banned.hsct-email.index',
            ],
            [
                'key' => 'inputasi',
                'label' => 'Inputasi',
                'route' => 'auto-banned.inputasi.index',
            ],
            [
                'key' => 'master-data',
                'label' => 'Master Data',
                'route' => 'auto-banned.master-data.index',
            ],
        ];
    }
}
