<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Carbon\CarbonInterface;

trait FormatsPlvDurasi
{
    public function plvDurasiDetik(?CarbonInterface $reference = null): int
    {
        if ($this->checkin_at === null) {
            return 0;
        }

        $end = $this->checkout_at ?? $reference ?? now();

        return max(0, (int) $this->checkin_at->diffInSeconds($end));
    }

    public function plvDurasiLabel(?CarbonInterface $reference = null): string
    {
        $totalSeconds = $this->plvDurasiDetik($reference);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        if ($hours >= 24) {
            $days = intdiv($hours, 24);
            $hours = $hours % 24;

            return sprintf('%dh %02d:%02d:%02d', $days, $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
