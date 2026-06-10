<?php

namespace App\Services\PembatasanLV;

use Carbon\Carbon;

class PembatasanLVShiftService
{
    public function resolveShift(?Carbon $at = null): int
    {
        $moment = ($at ?? now())->timezone(config('app.timezone'));
        $minutes = ((int) $moment->format('H')) * 60 + (int) $moment->format('i');

        // Shift 1: 06:00–17:59, Shift 2: 18:00–05:59
        return ($minutes >= 360 && $minutes < 1080) ? 1 : 2;
    }

    public function shiftLabel(int $shift): string
    {
        return 'Shift '.$shift;
    }
}
