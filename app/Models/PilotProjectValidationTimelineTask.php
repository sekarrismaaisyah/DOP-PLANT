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
        'original_owner',
        'original_status',
        'pic_actual_owner',
        'pic_start_date',
        'pic_actual_percent',
        'pic_progress_note',
        'evidence_link',
        'target_date',
        'dependency_blocker',
        'task_progress_percent_normalized',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'pic_start_date' => 'date',
            'target_date' => 'date',
            'pic_actual_percent' => 'decimal:2',
            'task_progress_percent_normalized' => 'decimal:2',
        ];
    }

    public function roadmapPeriod(): BelongsTo
    {
        return $this->belongsTo(PilotProjectValidationRoadmapPeriod::class, 'roadmap_period_id');
    }
}
