<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Support\DopSafety\DopSafetyProgramDefinition;
use Illuminate\View\View;

class DopSafetyFgdController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function index(): View
    {
        return view('DopSafety.fgd.index', $this->dopSafetyViewData('fgd', [
            'schedule' => DopSafetyProgramDefinition::fgdSchedule(),
            'participants' => ['Mekanik', 'GL', 'SH'],
            'outputs' => ['Risalah FGD', 'Action plan tertulis'],
            'sessions' => $this->demoFgdSessions(),
        ]));
    }

    /**
     * @return list<array{id: int, period: string, theme: string, status: string, action_items: int}>
     */
    private function demoFgdSessions(): array
    {
        return [
            [
                'id' => 1,
                'period' => 'Minggu 1–2',
                'theme' => 'Penyusunan JSA',
                'status' => 'Selesai',
                'action_items' => 5,
            ],
            [
                'id' => 2,
                'period' => 'Minggu 3–4',
                'theme' => 'Standar Tools',
                'status' => 'Berjalan',
                'action_items' => 3,
            ],
        ];
    }
}
