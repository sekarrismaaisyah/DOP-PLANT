<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoBannedPollLog extends Model
{
    protected $table = 'auto_banned_poll_logs';

    protected $fillable = [
        'rows_processed',
        'new_snapshots',
        'status_changes',
        'poll_started_at',
        'poll_finished_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'poll_started_at' => 'datetime',
        'poll_finished_at' => 'datetime',
    ];
}
