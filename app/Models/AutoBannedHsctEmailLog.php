<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AutoBannedHsctEmailType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoBannedHsctEmailLog extends Model
{
    protected $table = 'auto_banned_hsct_email_logs';

    protected $fillable = [
        'campaign_id',
        'email_type',
        'reminder_number',
        'week',
        'iso_year',
        'recipients',
        'total_in_list',
        'pending_count',
        'confirmed_count',
        'payload',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'email_type' => AutoBannedHsctEmailType::class,
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AutoBannedHsctCampaign::class, 'campaign_id');
    }
}
