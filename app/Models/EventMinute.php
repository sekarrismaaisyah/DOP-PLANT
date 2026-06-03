<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventMinute extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'notulis',
        'location',
        'issue_sections',
        'updated_by',
    ];

    protected $casts = [
        'issue_sections' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(MinuteIssue::class);
    }
}
