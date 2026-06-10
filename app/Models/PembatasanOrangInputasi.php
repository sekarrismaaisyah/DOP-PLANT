<?php

namespace App\Models;

use App\Models\Concerns\FormatsPlvDurasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembatasanOrangInputasi extends Model
{
    use FormatsPlvDurasi;
    protected $table = 'pembatasan_orang_inputasi';

    protected $fillable = [
        'shift',
        'status',
        'sid',
        'nama',
        'nik',
        'nama_perusahaan',
        'site',
        'dept',
        'lokasi',
        'detail_lokasi',
        'creator_id',
        'creator_name',
        'control_room',
        'aktivitas',
        'checkin_at',
        'checkout_at',
        'checkout_by_id',
        'checkout_by_name',
        'catatan',
    ];

    protected $casts = [
        'shift' => 'integer',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
    ];

    public function isInsideArea(): bool
    {
        return $this->checkout_at === null;
    }

    public function checkoutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checkout_by_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
