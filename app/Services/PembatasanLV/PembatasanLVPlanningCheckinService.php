<?php

declare(strict_types=1);

namespace App\Services\PembatasanLV;

use App\Models\PembatasanLvInputasi;
use App\Models\PembatasanLvPlanning;
use App\Models\PembatasanOrangInputasi;
use App\Models\PembatasanOrangPlanning;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PembatasanLVPlanningCheckinService
{
    public function __construct(
        private readonly PembatasanLVControlRoomContextService $controlRoomContext,
        private readonly PembatasanLVKapasitasLokasiService $kapasitasLokasiService,
        private readonly PembatasanLVShiftService $shiftService,
    ) {}

    public function checkinLv(PembatasanLvPlanning $planning, ?User $user): PembatasanLvInputasi
    {
        $this->assertCanCheckin($planning->control_room, $planning->checked_in_at, $user);

        $kapasitas = $this->kapasitasLokasiService->check(
            (string) $planning->lokasi,
            $planning->detail_lokasi
        );

        if ($kapasitas['has_batas'] && ! $kapasitas['can_input']) {
            throw new RuntimeException($kapasitas['message'] ?? 'Kapasitas lokasi sudah penuh.');
        }

        return DB::transaction(function () use ($planning, $user): PembatasanLvInputasi {
            $planning->refresh();

            if ($planning->checked_in_at !== null) {
                throw new RuntimeException('Planning LV ini sudah di-check-in.');
            }

            $now = now()->timezone(config('app.timezone'));

            $inputasi = PembatasanLvInputasi::query()->create([
                'shift' => $this->shiftService->resolveShift($now),
                'status' => $planning->status,
                'nama_driver' => $planning->nama_driver,
                'driver_ref' => $planning->driver_ref,
                'no_lambung' => $planning->no_lambung,
                'id_unit' => $planning->id_unit,
                'lokasi' => $planning->lokasi,
                'detail_lokasi' => $planning->detail_lokasi,
                'creator_id' => $user?->id,
                'creator_name' => (string) ($user?->name ?? '—'),
                'control_room' => $planning->control_room,
                'aktivitas' => $planning->aktivitas,
                'checkin_at' => $now,
                'catatan' => $planning->catatan,
            ]);

            $planning->update(['checked_in_at' => $now]);

            return $inputasi;
        });
    }

    public function checkinOrang(PembatasanOrangPlanning $planning, ?User $user): PembatasanOrangInputasi
    {
        $this->assertCanCheckin($planning->control_room, $planning->checked_in_at, $user);

        return DB::transaction(function () use ($planning, $user): PembatasanOrangInputasi {
            $planning->refresh();

            if ($planning->checked_in_at !== null) {
                throw new RuntimeException('Planning orang ini sudah di-check-in.');
            }

            $now = now()->timezone(config('app.timezone'));

            $inputasi = PembatasanOrangInputasi::query()->create([
                'shift' => $this->shiftService->resolveShift($now),
                'status' => $planning->status,
                'sid' => $planning->sid,
                'nama' => $planning->nama,
                'nik' => $planning->nik,
                'nama_perusahaan' => $planning->nama_perusahaan,
                'site' => $planning->site,
                'dept' => $planning->dept,
                'lokasi' => $planning->lokasi,
                'detail_lokasi' => $planning->detail_lokasi,
                'creator_id' => $user?->id,
                'creator_name' => (string) ($user?->name ?? '—'),
                'control_room' => $planning->control_room,
                'aktivitas' => $planning->aktivitas,
                'checkin_at' => $now,
                'catatan' => $planning->catatan,
            ]);

            $planning->update(['checked_in_at' => $now]);

            return $inputasi;
        });
    }

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     * @return array{lv: \Illuminate\Support\Collection, orang: \Illuminate\Support\Collection}
     */
    public function pendingOverview(?User $user, array $filters): array
    {
        $rooms = $this->supervisedRooms($user, $filters);
        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        $date = $tanggal !== '' ? Carbon::parse($tanggal)->toDateString() : now()->toDateString();

        $lvQuery = PembatasanLvPlanning::query()
            ->whereNull('checked_in_at')
            ->whereDate('tanggal_plan', $date)
            ->orderBy('shift')
            ->orderBy('no_lambung');

        $orangQuery = PembatasanOrangPlanning::query()
            ->whereNull('checked_in_at')
            ->whereDate('tanggal_plan', $date)
            ->orderBy('shift')
            ->orderBy('nama');

        if ($rooms->isEmpty()) {
            return ['lv' => collect(), 'orang' => collect()];
        }

        $lvQuery->whereIn('control_room', $rooms->all());
        $orangQuery->whereIn('control_room', $rooms->all());

        return [
            'lv' => $lvQuery->limit(200)->get(),
            'orang' => $orangQuery->limit(200)->get(),
        ];
    }

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    private function supervisedRooms(?User $user, array $filters): \Illuminate\Support\Collection
    {
        $rooms = $this->controlRoomContext->controlRoomsForUser($user);
        $filterRoom = trim((string) ($filters['control_room'] ?? ''));

        if ($filterRoom !== '') {
            return $rooms->filter(fn (string $room) => strcasecmp($room, $filterRoom) === 0)->values();
        }

        return $rooms;
    }

    private function assertCanCheckin(string $controlRoom, mixed $checkedInAt, ?User $user): void
    {
        if ($checkedInAt !== null) {
            throw new RuntimeException('Data planning sudah di-check-in.');
        }

        $allowed = $this->controlRoomContext
            ->controlRoomsForUser($user)
            ->contains(fn (string $room) => strcasecmp($room, $controlRoom) === 0);

        if (! $allowed) {
            throw new RuntimeException('Anda tidak berwenang check-in untuk control room ini.');
        }
    }
}
