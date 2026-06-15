<?php

declare(strict_types=1);

namespace App\Http\Controllers\PembatasanLV\Concerns;

use App\Models\PembatasanMasterAktivitas;
use App\Services\PembatasanLV\PembatasanLVControlRoomContextService;
use App\Services\PembatasanLV\PembatasanLVShiftService;
use Illuminate\Support\Collection;

trait ProvidesPembatasanLVInputasiFormContext
{
    /**
     * @return array{
     *     shift: int,
     *     shift_label: string,
     *     checkin_at: string,
     *     checkin_display: string,
     *     creator_name: string,
     *     creator_id: int|null,
     *     control_room: string|null,
     *     control_rooms: array<int, string>,
     *     timezone: string
     * }
     */
    protected function pembatasanLvInputasiFormContext(
        PembatasanLVShiftService $shiftService,
        PembatasanLVControlRoomContextService $controlRoomContext,
        mixed $user,
    ): array {
        $now = now()->timezone(config('app.timezone'));
        $controlRooms = $controlRoomContext->controlRoomsForUser($user);
        $shift = $shiftService->resolveShift($now);

        return [
            'shift' => $shift,
            'shift_label' => $shiftService->shiftLabel($shift),
            'checkin_at' => $now->format('Y-m-d\TH:i'),
            'checkin_display' => $now->format('d M Y H:i'),
            'creator_name' => (string) ($user?->name ?? '—'),
            'creator_id' => $user?->id,
            'control_room' => $controlRooms->first(),
            'control_rooms' => $controlRooms->all(),
            'timezone' => config('app.timezone'),
        ];
    }

    protected function pembatasanLvAktivitasOptions(): Collection
    {
        return PembatasanMasterAktivitas::query()
            ->orderBy('site')
            ->orderBy('detail_aktivitas_pengoperasian_lv')
            ->pluck('detail_aktivitas_pengoperasian_lv')
            ->unique()
            ->values();
    }
}
