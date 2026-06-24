<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoBannedDailyEmailLog extends Model
{
    protected $table = 'auto_banned_daily_email_logs';

    protected $fillable = [
        'filter_date',
        'filter_shift',
        'scraped_at',
        'recipients',
        'total_banned',
        'perusahaan_count',
        'site_count',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'filter_date' => 'date',
        'scraped_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
