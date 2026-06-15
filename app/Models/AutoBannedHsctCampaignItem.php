<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoBannedHsctCampaignItem extends Model
{
    protected $table = 'auto_banned_hsct_campaign_items';

    protected $fillable = [
        'campaign_id',
        'snapshot_id',
        'sid',
        'karyawan',
        'perusahaan',
        'site_dedicated',
        'banned_reason',
        'is_confirmed',
        'confirmed_at',
    ];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AutoBannedHsctCampaign::class, 'campaign_id');
    }
}
