<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotProjectValidationTimelineTask extends Model
{
    protected $table = 'pilot_project_validation_timeline_tasks';

    protected $fillable = [
        'roadmap_period_id',
        'sort_order',
        'task_text',
        'task_owner',
        'task_status',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function roadmapPeriod(): BelongsTo
    {
        return $this->belongsTo(PilotProjectValidationRoadmapPeriod::class, 'roadmap_period_id');
    }
}
