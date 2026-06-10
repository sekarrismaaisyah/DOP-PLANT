<?php

namespace App\Services\PembatasanLV;

use App\Models\PembatasanLvInputasi;
use App\Models\PembatasanOrangInputasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PembatasanLVOverviewService
{
    public function __construct(
        private readonly PembatasanLVControlRoomContextService $controlRoomContext,
    ) {}

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function supervisedRooms(?User $user, array $filters): Collection
    {
        $rooms = $this->controlRoomContext->controlRoomsForUser($user);

        $filterRoom = trim((string) ($filters['control_room'] ?? ''));
        if ($filterRoom !== '') {
            return $rooms->filter(fn (string $room) => strcasecmp($room, $filterRoom) === 0)->values();
        }

        return $rooms;
    }

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function baseQuery(?User $user, array $filters): Builder
    {
        $rooms = $this->supervisedRooms($user, $filters);

        $query = PembatasanLvInputasi::query()->orderByDesc('checkin_at');

        if ($rooms->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('control_room', $rooms->all());
    }

    /**
     * LV masuk & masih di area (belum checkout).
     *
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function lvMasukAktifQuery(?User $user, array $filters): Builder
    {
        return $this->baseQuery($user, $filters)->whereNull('checkout_at');
    }

    /**
     * LV sudah checkout pada tanggal filter.
     *
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function lvKeluarQuery(?User $user, array $filters): Builder
    {
        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        $date = $tanggal !== '' ? Carbon::parse($tanggal)->toDateString() : now()->toDateString();

        return $this->baseQuery($user, $filters)
            ->whereNotNull('checkout_at')
            ->whereDate('checkout_at', $date);
    }

    /**
     * Semua inputasi LV (check-in & check-out) untuk control room pengawas.
     *
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function lvAllListQuery(?User $user, array $filters): Builder
    {
        $query = $this->baseQuery($user, $filters);

        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        if ($tanggal !== '') {
            $date = Carbon::parse($tanggal)->toDateString();
            $query->where(function (Builder $builder) use ($date): void {
                $builder->whereDate('checkin_at', $date)
                    ->orWhereDate('checkout_at', $date);
            });
        }

        return $query;
    }

    public function userCanManageRecord(?User $user, PembatasanLvInputasi $record): bool
    {
        return $this->controlRoomContext
            ->controlRoomsForUser($user)
            ->contains(fn (string $room) => strcasecmp($room, (string) $record->control_room) === 0);
    }

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function orangBaseQuery(?User $user, array $filters): Builder
    {
        $rooms = $this->supervisedRooms($user, $filters);

        $query = PembatasanOrangInputasi::query()->orderByDesc('checkin_at');

        if ($rooms->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('control_room', $rooms->all());
    }

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function orangMasukAktifQuery(?User $user, array $filters): Builder
    {
        return $this->orangBaseQuery($user, $filters)->whereNull('checkout_at');
    }

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function orangKeluarQuery(?User $user, array $filters): Builder
    {
        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        $date = $tanggal !== '' ? Carbon::parse($tanggal)->toDateString() : now()->toDateString();

        return $this->orangBaseQuery($user, $filters)
            ->whereNotNull('checkout_at')
            ->whereDate('checkout_at', $date);
    }

    /**
     * @param  array{site?: string, tanggal?: string, control_room?: string}  $filters
     */
    public function orangAllListQuery(?User $user, array $filters): Builder
    {
        $query = $this->orangBaseQuery($user, $filters);

        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        if ($tanggal !== '') {
            $date = Carbon::parse($tanggal)->toDateString();
            $query->where(function (Builder $builder) use ($date): void {
                $builder->whereDate('checkin_at', $date)
                    ->orWhereDate('checkout_at', $date);
            });
        }

        return $query;
    }

    public function userCanManageOrangRecord(?User $user, PembatasanOrangInputasi $record): bool
    {
        return $this->controlRoomContext
            ->controlRoomsForUser($user)
            ->contains(fn (string $room) => strcasecmp($room, (string) $record->control_room) === 0);
    }
}
