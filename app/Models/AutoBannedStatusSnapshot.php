<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AutoBannedBanStatus;
use App\Enums\AutoBannedHsctSyncStatus;
use App\Enums\AutoBannedSystemStatus;
use App\Enums\AutoBannedTreatmentStatus;
use App\Enums\AutoBannedVerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutoBannedStatusSnapshot extends Model
{
    protected $table = 'auto_banned_status_snapshots';

    protected $fillable = [
        'sid',
        'week',
        'iso_year',
        'karyawan',
        'perusahaan',
        'site_dedicated',
        'banned_reason',
        'system_status',
        'scrap_status_raw',
        'scr_row_id',
        'ban_status',
        'treatment_status',
        'verification_status',
        'hsct_sync_status',
        'first_seen_at',
        'last_seen_at',
        'status_changed_at',
        'banned_detected_at',
        'hsct_sent_at',
        'hsct_confirmed_at',
        'treatment_submitted_at',
        'verification_done_at',
        'unban_opened_at',
        'unban_closed_at',
        'scr_scraped_at',
    ];

    protected $casts = [
        'system_status' => AutoBannedSystemStatus::class,
        'ban_status' => AutoBannedBanStatus::class,
        'treatment_status' => AutoBannedTreatmentStatus::class,
        'verification_status' => AutoBannedVerificationStatus::class,
        'hsct_sync_status' => AutoBannedHsctSyncStatus::class,
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'banned_detected_at' => 'datetime',
        'hsct_sent_at' => 'datetime',
        'hsct_confirmed_at' => 'datetime',
        'treatment_submitted_at' => 'datetime',
        'verification_done_at' => 'datetime',
        'unban_opened_at' => 'datetime',
        'unban_closed_at' => 'datetime',
        'scr_scraped_at' => 'datetime',
    ];

    public function changes(): HasMany
    {
        return $this->hasMany(AutoBannedStatusChange::class, 'snapshot_id');
    }
}
