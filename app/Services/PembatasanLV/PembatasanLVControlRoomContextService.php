<?php

namespace App\Services\PembatasanLV;

use App\Models\CctvControlRoomPengawas;
use App\Models\User;
use Illuminate\Support\Collection;

class PembatasanLVControlRoomContextService
{
    public function controlRoomsForUser(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        $email = mb_strtolower(trim((string) $user->email));
        $name = mb_strtolower(trim((string) $user->name));

        return CctvControlRoomPengawas::query()
            ->where(function ($query) use ($email, $name): void {
                if ($email !== '') {
                    $query->whereRaw('LOWER(TRIM(email_pengawas)) = ?', [$email]);
                }
                if ($name !== '') {
                    $query->orWhereRaw('LOWER(TRIM(nama_pengawas)) = ?', [$name]);
                }
            })
            ->orderBy('control_room')
            ->pluck('control_room')
            ->map(fn ($room) => trim((string) $room))
            ->filter()
            ->unique()
            ->values();
    }

    public function primaryControlRoom(?User $user): ?string
    {
        return $this->controlRoomsForUser($user)->first();
    }
}
