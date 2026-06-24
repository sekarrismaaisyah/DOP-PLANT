<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AutoBannedSidAutomationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SidBannedLog extends Model
{
    protected $table = 'sid_banned_log';

    protected $guarded = [];

    protected $casts = [
        'filter_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'automation_status' => AutoBannedSidAutomationStatus::class,
    ];

    public function scrDailyBanned(): BelongsTo
    {
        return $this->belongsTo(ScrDailyBanned::class, 'scr_daily_banned_id');
    }
}
