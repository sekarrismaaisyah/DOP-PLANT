<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RosterPlanningJob extends Model
{
    use HasFactory;

    protected $table = 'roster_planning_jobs';

    protected $fillable = [
        'job_id',
        'start_date',
        'end_date',
        'status',
        'dop_created',
        'dop_updated',
        'ikk_created',
        'ikk_updated',
        'error_message',
        'user_id',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getTotalGeneratedAttribute(): int
    {
        return $this->dop_created + $this->dop_updated + $this->ikk_created + $this->ikk_updated;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
