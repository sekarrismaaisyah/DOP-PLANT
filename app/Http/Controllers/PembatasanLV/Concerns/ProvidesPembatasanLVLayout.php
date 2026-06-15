<?php

namespace App\Http\Controllers\PembatasanLV\Concerns;

trait ProvidesPembatasanLVLayout
{
    protected function pembatasanLvNavItems(): array
    {
        return [
            [
                'key' => 'overview',
                'label' => 'Overview',
                'route' => 'pembatasan-lv.index',
            ],
            [
                'key' => 'inputasi',
                'label' => 'Inputasi',
                'route' => 'pembatasan-lv.inputasi.index',
            ],
            [
                'key' => 'planning',
                'label' => 'Planning',
                'route' => 'pembatasan-lv.planning.index',
            ],
            [
                'key' => 'master-data',
                'label' => 'Master Data',
                'route' => 'pembatasan-lv.master-data.index',
            ],
        ];
    }
}
