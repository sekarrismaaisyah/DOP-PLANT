<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinuteIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_minute_id',
        'section',
        'nomor',
        'catatan_meeting',
        'issued_by',
        'pic',
        'due_date',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    protected $appends = [
        'computed_status',
    ];

    public function eventMinute(): BelongsTo
    {
        return $this->belongsTo(EventMinute::class);
    }

    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'Closed') {
            return 'Closed';
        }

        if ($this->due_date && $this->due_date->isPast()) {
            return 'Overdue';
        }

        return $this->status;
    }
}
