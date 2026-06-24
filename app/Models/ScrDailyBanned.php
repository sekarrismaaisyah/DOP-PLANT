<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScrDailyBanned extends Model
{
    protected $table = 'scr_daily_banned';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'scraped_at' => 'datetime',
        'filter_date' => 'date',
    ];

    public function bannedLog(): HasOne
    {
        return $this->hasOne(SidBannedLog::class, 'scr_daily_banned_id');
    }

    public function unbanRequests(): HasMany
    {
        return $this->hasMany(AutoBannedUnbanRequest::class, 'scr_daily_banned_id');
    }
}
