<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AutoBannedUnbanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoBannedUnbanRequest extends Model
{
    protected $table = 'auto_banned_unban_requests';

    protected $fillable = [
        'sid',
        'karyawan',
        'perusahaan',
        'site_dedicated',
        'banned_reason',
        'status_banned_ref',
        'alasan_pengajuan',
        'status',
        'week',
        'iso_year',
        'submitted_by_id',
        'submitted_by_name',
        'reviewed_by_id',
        'reviewed_by_name',
        'reviewed_at',
        'catatan_review',
    ];

    protected $casts = [
        'status' => AutoBannedUnbanStatus::class,
        'reviewed_at' => 'datetime',
    ];

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }
}
