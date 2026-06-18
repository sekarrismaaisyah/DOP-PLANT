<?php

declare(strict_types=1);

namespace App\Support\FatigueManagement;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Slot upload evidence — jendela waktu per frekuensi.
 *
 * Shift 1: 06:00–18:00 · Shift 2: 18:00–06:00 (hari berikutnya)
 * Daily: hanya slot hari berjalan · Weekly: bebas Sen–Min dalam minggu ISO
 */
final class FatigueManagementFrequencyPlan
{
    public const MODE_SHIFT_PER_DAY = 'shift_per_day';

    public const MODE_DAILY = 'daily';

    public const MODE_WEEKLY_COUNT = 'weekly_count';

    public const MODE_WEEKLY_ONCE = 'weekly_once';

    public const SHIFT_1_START_HOUR = 6;

    public const SHIFT_1_END_HOUR = 18;

    private const DAY_LABELS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    /**
     * @return array{mode: string, per_day: int, per_week: int, category: string, category_label: string}
     */
    public static function resolve(string $frequencyRaw): array
    {
        $lower = mb_strtolower(trim(str_replace("\n", ' ', $frequencyRaw)));
        $lower = preg_replace('/\s+/u', ' ', $lower) ?? $lower;

        if (preg_match('/(\d+)\s*x\s*\/\s*minggu|(\d+)\s*x\s*per\s*week|(\d+)x\s*\/\s*week|(\d+)x\s*minggu/u', $lower, $m)) {
            $count = (int) ($m[1] ?: $m[2] ?: $m[3] ?: $m[4]);

            return [
                'mode' => self::MODE_WEEKLY_COUNT,
                'per_day' => 0,
                'per_week' => max(1, $count),
                'category' => 'weekly',
                'category_label' => 'Mingguan (' . max(1, $count) . '×/minggu)',
            ];
        }

        if (preg_match('/(\d+)\s*x\s*\/\s*day|(\d+)\s*x\s*per\s*day|(\d+)x\s*\/\s*hari/u', $lower, $m)) {
            $perDay = (int) ($m[1] ?: $m[2] ?: $m[3]);

            return [
                'mode' => $perDay >= 2 ? self::MODE_SHIFT_PER_DAY : self::MODE_DAILY,
                'per_day' => max(1, $perDay),
                'per_week' => 0,
                'category' => $perDay >= 2 ? 'shift' : 'daily',
                'category_label' => $perDay >= 2 ? 'Shift (' . $perDay . '×/hari)' : 'Harian',
            ];
        }

        if (str_contains($lower, 'pagi') && str_contains($lower, 'siang')) {
            return [
                'mode' => self::MODE_SHIFT_PER_DAY,
                'per_day' => 2,
                'per_week' => 0,
                'category' => 'shift',
                'category_label' => 'Shift (2×/hari)',
            ];
        }

        if (str_contains($lower, 'shift') || str_contains($lower, 'shiftly')) {
            return [
                'mode' => self::MODE_SHIFT_PER_DAY,
                'per_day' => 2,
                'per_week' => 0,
                'category' => 'shift',
                'category_label' => 'Shift (2×/hari)',
            ];
        }

        if (str_contains($lower, 'daily') || str_contains($lower, 'harian') || str_contains($lower, 'awal shift')) {
            return [
                'mode' => self::MODE_DAILY,
                'per_day' => 1,
                'per_week' => 0,
                'category' => 'daily',
                'category_label' => 'Harian',
            ];
        }

        if (str_contains($lower, 'weekly') || str_contains($lower, 'minggu') || str_contains($lower, 'week')) {
            return [
                'mode' => self::MODE_WEEKLY_ONCE,
                'per_day' => 0,
                'per_week' => 1,
                'category' => 'weekly',
                'category_label' => 'Mingguan (1×/minggu)',
            ];
        }

        return [
            'mode' => self::MODE_WEEKLY_ONCE,
            'per_day' => 0,
            'per_week' => 1,
            'category' => 'weekly',
            'category_label' => 'Mingguan',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function slotsForWeek(string $frequencyRaw, int $year, string $isoWeek): array
    {
        $plan = self::resolve($frequencyRaw);

        return match ($plan['mode']) {
            self::MODE_SHIFT_PER_DAY => self::shiftSlots($year, $isoWeek, (int) $plan['per_day']),
            self::MODE_DAILY => self::dailySlots($year, $isoWeek),
            self::MODE_WEEKLY_COUNT => self::weeklyCountSlots((int) $plan['per_week']),
            default => self::weeklyCountSlots(1),
        };
    }

    /**
     * Konteks waktu untuk header halaman upload.
     *
     * @return array<string, mixed>
     */
    public static function uploadPageContext(int $year, string $isoWeek, ?Carbon $now = null): array
    {
        $now = self::appNow($now);
        $relation = self::weekRelation($year, $isoWeek, $now);
        $todayIndex = self::dayIndexInIsoWeek($now, $year, $isoWeek);
        $activeShift = self::activeShiftSlot($year, $isoWeek, $now);

        return [
            'now' => $now->format('d M Y H:i'),
            'timezone' => (string) config('app.timezone', 'Asia/Makassar'),
            'iso_week' => $isoWeek,
            'year' => $year,
            'week_relation' => $relation,
            'week_relation_label' => match ($relation) {
                'past' => 'Minggu lampau — semua slot terbuka untuk pelengkapan',
                'future' => 'Minggu mendatang — upload belum dibuka',
                default => 'Minggu berjalan — slot mengikuti hari & jam saat ini',
            },
            'today_index' => $todayIndex,
            'today_label' => $todayIndex !== null ? self::DAY_LABELS[$todayIndex - 1] : null,
            'active_shift' => $activeShift,
            'current_shift_number' => self::currentShiftNumber($now),
        ];
    }

    /**
     * @param  array<string, mixed>  $slotDef
     * @param  array<string, mixed>  $plan
     * @return array{visible: bool, uploadable: bool, time_window: string, hint: string, is_active: bool}
     */
    public static function slotUploadContext(
        array $slotDef,
        array $plan,
        int $year,
        string $isoWeek,
        bool $isDone,
        ?Carbon $now = null,
    ): array {
        $now = self::appNow($now);
        $relation = self::weekRelation($year, $isoWeek, $now);
        $mode = $plan['mode'] ?? self::MODE_WEEKLY_ONCE;
        $timeWindow = (string) ($slotDef['time_window'] ?? '');

        if ($isDone) {
            $visible = self::doneSlotVisible($slotDef, $mode, $year, $isoWeek, $now);

            return [
                'visible' => $visible,
                'uploadable' => $visible && $relation !== 'future',
                'time_window' => $timeWindow,
                'hint' => 'Sudah upload — klik untuk ganti',
                'is_active' => false,
            ];
        }

        if ($relation === 'future') {
            return [
                'visible' => in_array($mode, [self::MODE_WEEKLY_COUNT, self::MODE_WEEKLY_ONCE], true),
                'uploadable' => false,
                'time_window' => $timeWindow,
                'hint' => 'Minggu ini belum dimulai',
                'is_active' => false,
            ];
        }

        if (in_array($mode, [self::MODE_WEEKLY_COUNT, self::MODE_WEEKLY_ONCE], true)) {
            return [
                'visible' => true,
                'uploadable' => ! $isDone,
                'time_window' => 'Sen–Min (bebas)',
                'hint' => $isDone ? 'Sudah upload' : 'Upload kapan saja dalam minggu ini',
                'is_active' => ! $isDone,
            ];
        }

        if ($relation === 'past') {
            return [
                'visible' => true,
                'uploadable' => ! $isDone,
                'time_window' => $timeWindow,
                'hint' => $isDone ? 'Sudah upload' : 'Pelengkapan minggu lampau',
                'is_active' => ! $isDone,
            ];
        }

        // Minggu berjalan — aturan hari & jam
        if ($mode === self::MODE_DAILY) {
            $todayIndex = self::dayIndexInIsoWeek($now, $year, $isoWeek);
            $slotDay = (int) ($slotDef['day_index'] ?? 0);
            $isToday = $todayIndex !== null && $slotDay === $todayIndex;
            $visible = $isDone || $isToday;
            $uploadable = $isToday && ! $isDone;

            return [
                'visible' => $visible,
                'uploadable' => $uploadable,
                'time_window' => 'Hari ini (00:00–23:59)',
                'hint' => $isDone
                    ? 'Sudah upload'
                    : ($isToday ? 'Slot aktif hari ini' : 'Muncul pada hari tersebut'),
                'is_active' => $isToday && ! $isDone,
            ];
        }

        if ($mode === self::MODE_SHIFT_PER_DAY) {
            $active = self::activeShiftSlot($year, $isoWeek, $now);
            $slotKey = (string) ($slotDef['key'] ?? '');
            $isActive = $active !== null && $active['key'] === $slotKey;
            $visible = $isDone || $isActive;

            if ($isActive && ! $isDone) {
                $hint = 'Slot aktif sekarang';
            } elseif ($isDone) {
                $hint = 'Sudah upload';
            } elseif ((int) ($slotDef['shift_index'] ?? 0) === 1) {
                $hint = 'Aktif pukul 06:00–18:00';
            } else {
                $hint = 'Aktif pukul 18:00–06:00';
            }

            return [
                'visible' => $visible,
                'uploadable' => $isActive && ! $isDone,
                'time_window' => $timeWindow,
                'hint' => $hint,
                'is_active' => $isActive && ! $isDone,
            ];
        }

        return [
            'visible' => true,
            'uploadable' => ! $isDone,
            'time_window' => $timeWindow,
            'hint' => '',
            'is_active' => ! $isDone,
        ];
    }

    public static function assertSlotUploadable(
        string $frequencyRaw,
        string $slotKey,
        int $year,
        string $isoWeek,
        ?Carbon $now = null,
    ): void {
        $plan = self::resolve($frequencyRaw);
        $slots = self::slotsForWeek($frequencyRaw, $year, $isoWeek);
        $slotDef = null;
        foreach ($slots as $slot) {
            if (($slot['key'] ?? '') === $slotKey) {
                $slotDef = $slot;
                break;
            }
        }

        if ($slotDef === null) {
            throw ValidationException::withMessages([
                'frequency_slot' => ['Slot upload tidak valid.'],
            ]);
        }

        $ctx = self::slotUploadContext($slotDef, $plan, $year, $isoWeek, false, $now);
        if (! ($ctx['uploadable'] ?? false)) {
            throw ValidationException::withMessages([
                'frequency_slot' => [
                    'Slot "' . ($slotDef['label'] ?? $slotKey) . '" belum aktif. '
                    . ($ctx['hint'] ?? 'Cek jadwal shift/hari.'),
                ],
            ]);
        }
    }

    /**
     * @param  list<array{done: bool}>  $slotStates
     */
    public static function isChecklistMet(array $slotStates): bool
    {
        if ($slotStates === []) {
            return false;
        }

        foreach ($slotStates as $slot) {
            if (! ($slot['done'] ?? false)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function uploadGroups(): array
    {
        return [
            [
                'key' => 'shift',
                'label' => 'Shift',
                'description' => 'Shift 1 (06:00–18:00) & Shift 2 (18:00–06:00) — slot muncul sesuai jam',
                'order' => 1,
            ],
            [
                'key' => 'daily',
                'label' => 'Harian',
                'description' => '1 upload per hari — hanya slot hari ini yang aktif',
                'order' => 2,
            ],
            [
                'key' => 'weekly',
                'label' => 'Mingguan',
                'description' => 'Bebas Sen–Min dalam minggu ISO terpilih',
                'order' => 3,
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, day_index: int, shift_index: int, time_window: string}|null
     */
    public static function activeShiftSlot(int $year, string $isoWeek, ?Carbon $now = null): ?array
    {
        $now = self::appNow($now);
        $shiftNumber = self::currentShiftNumber($now);

        if ($shiftNumber === 1) {
            $anchor = $now->copy();
        } else {
            $anchor = $now->hour < self::SHIFT_1_START_HOUR
                ? $now->copy()->subDay()
                : $now->copy();
        }

        $dayIndex = self::dayIndexInIsoWeek($anchor, $year, $isoWeek);
        if ($dayIndex === null) {
            return null;
        }

        $dayLabel = self::DAY_LABELS[$dayIndex - 1];

        return [
            'key' => sprintf('d%d-s%d', $dayIndex, $shiftNumber),
            'label' => $dayLabel . ' · Shift ' . $shiftNumber,
            'day_index' => $dayIndex,
            'shift_index' => $shiftNumber,
            'time_window' => $shiftNumber === 1 ? '06:00–18:00' : '18:00–06:00',
        ];
    }

    public static function currentShiftNumber(Carbon $now): int
    {
        $hour = (int) $now->format('G');

        return ($hour >= self::SHIFT_1_START_HOUR && $hour < self::SHIFT_1_END_HOUR) ? 1 : 2;
    }

    public static function dayIndexInIsoWeek(Carbon $date, int $year, string $isoWeek): ?int
    {
        $weekStart = self::weekStart($year, $isoWeek)->startOfDay();
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
        $target = $date->copy()->timezone($weekStart->timezone)->startOfDay();

        if (! $target->betweenIncluded($weekStart, $weekEnd)) {
            return null;
        }

        return (int) $weekStart->diffInDays($target) + 1;
    }

    public static function weekRelation(int $year, string $isoWeek, ?Carbon $now = null): string
    {
        $now = self::appNow($now);
        $currentWeek = (int) $now->isoWeek();
        $currentYear = (int) $now->isoWeekYear();

        if (! preg_match('/^W(\d{2})$/', $isoWeek, $m)) {
            return 'current';
        }

        $targetWeek = (int) $m[1];

        if ($year < $currentYear || ($year === $currentYear && $targetWeek < $currentWeek)) {
            return 'past';
        }

        if ($year > $currentYear || ($year === $currentYear && $targetWeek > $currentWeek)) {
            return 'future';
        }

        return 'current';
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function shiftSlots(int $year, string $isoWeek, int $shiftsPerDay): array
    {
        $slots = [];
        $weekStart = self::weekStart($year, $isoWeek);

        for ($day = 0; $day < 7; $day++) {
            $date = $weekStart->copy()->addDays($day);
            $dayLabel = self::DAY_LABELS[$day];

            for ($shift = 1; $shift <= $shiftsPerDay; $shift++) {
                $slots[] = [
                    'key' => sprintf('d%d-s%d', $day + 1, $shift),
                    'label' => $dayLabel . ' · Shift ' . $shift,
                    'day_index' => $day + 1,
                    'shift_index' => $shift,
                    'date_label' => $date->format('d M'),
                    'time_window' => $shift === 1 ? '06:00–18:00' : '18:00–06:00',
                ];
            }
        }

        return $slots;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function dailySlots(int $year, string $isoWeek): array
    {
        $slots = [];
        $weekStart = self::weekStart($year, $isoWeek);

        for ($day = 0; $day < 7; $day++) {
            $date = $weekStart->copy()->addDays($day);
            $slots[] = [
                'key' => 'd' . ($day + 1),
                'label' => self::DAY_LABELS[$day],
                'day_index' => $day + 1,
                'shift_index' => null,
                'date_label' => $date->format('d M'),
                'time_window' => 'Hari penuh',
            ];
        }

        return $slots;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function weeklyCountSlots(int $count): array
    {
        $slots = [];
        for ($i = 1; $i <= $count; $i++) {
            $slots[] = [
                'key' => 'wk-' . $i,
                'label' => 'Upload ' . $i,
                'day_index' => null,
                'shift_index' => null,
                'date_label' => null,
                'time_window' => 'Sen–Min',
            ];
        }

        return $slots;
    }

    private static function weekStart(int $year, string $isoWeek): Carbon
    {
        if (! preg_match('/^W(\d{2})$/', $isoWeek, $m)) {
            return self::appNow()->startOfWeek(Carbon::MONDAY);
        }

        return self::appNow()
            ->copy()
            ->setISODate($year, (int) $m[1])
            ->startOfWeek(Carbon::MONDAY)
            ->startOfDay();
    }

    private static function doneSlotVisible(
        array $slotDef,
        string $mode,
        int $year,
        string $isoWeek,
        Carbon $now,
    ): bool {
        if (in_array($mode, [self::MODE_WEEKLY_COUNT, self::MODE_WEEKLY_ONCE], true)) {
            return true;
        }

        if ($mode === self::MODE_DAILY) {
            return true;
        }

        if ($mode === self::MODE_SHIFT_PER_DAY) {
            $active = self::activeShiftSlot($year, $isoWeek, $now);
            if ($active === null) {
                return false;
            }

            return (int) ($slotDef['day_index'] ?? 0) === (int) $active['day_index'];
        }

        return true;
    }

    private static function appNow(?Carbon $now = null): Carbon
    {
        return ($now ?? now())->copy()->timezone(config('app.timezone', 'Asia/Makassar'));
    }
}
