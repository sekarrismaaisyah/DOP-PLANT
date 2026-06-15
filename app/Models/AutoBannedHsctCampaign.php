<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AutoBannedHsctCampaignStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutoBannedHsctCampaign extends Model
{
    protected $table = 'auto_banned_hsct_campaigns';

    protected $fillable = [
        'week',
        'iso_year',
        'status',
        'total_items',
        'confirmed_items',
        'reminder_count',
        'initial_sent_at',
        'last_reminder_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => AutoBannedHsctCampaignStatus::class,
        'initial_sent_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(AutoBannedHsctCampaignItem::class, 'campaign_id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(AutoBannedHsctEmailLog::class, 'campaign_id');
    }
}
