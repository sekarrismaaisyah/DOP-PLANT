<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AutoBannedStatusChangeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoBannedStatusChange extends Model
{
    protected $table = 'auto_banned_status_changes';

    protected $fillable = [
        'snapshot_id',
        'sid',
        'week',
        'iso_year',
        'from_system_status',
        'to_system_status',
        'change_type',
        'scrap_status_raw',
        'scr_row_id',
        'detected_at',
        'scr_scraped_at',
    ];

    protected $casts = [
        'change_type' => AutoBannedStatusChangeType::class,
        'detected_at' => 'datetime',
        'scr_scraped_at' => 'datetime',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(AutoBannedStatusSnapshot::class, 'snapshot_id');
    }
}
