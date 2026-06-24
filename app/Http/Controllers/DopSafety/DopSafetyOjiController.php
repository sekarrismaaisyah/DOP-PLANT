<?php

declare(strict_types=1);

namespace App\Http\Controllers\DopSafety;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DopSafety\Concerns\ProvidesDopSafetyLayout;
use App\Support\DopSafety\DopSafetyProgramDefinition;
use Illuminate\View\View;

class DopSafetyOjiController extends Controller
{
    use ProvidesDopSafetyLayout;

    public function index(): View
    {
        return view('DopSafety.oji.index', $this->dopSafetyViewData('oji', [
            'rules' => DopSafetyProgramDefinition::ojiRules(),
            'pendingItems' => $this->demoPendingOji(),
        ]));
    }

    /**
     * @return list<array{id: int, section: string, gl: string, shift: string, status: string, submitted_at: string}>
     */
    private function demoPendingOji(): array
    {
        return [
            [
                'id' => 1,
                'section' => 'Wheel',
                'gl' => 'Budi Santoso',
                'shift' => 'Shift 1',
                'status' => 'Menunggu Approval SH',
                'submitted_at' => now()->subMinutes(45)->format('d M Y H:i'),
            ],
            [
                'id' => 2,
                'section' => 'Track',
                'gl' => 'Andi Pratama',
                'shift' => 'Shift 1',
                'status' => 'Approved',
                'submitted_at' => now()->subHours(2)->format('d M Y H:i'),
            ],
        ];
    }
}
