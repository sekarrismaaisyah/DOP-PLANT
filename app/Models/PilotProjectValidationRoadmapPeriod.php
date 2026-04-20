<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PilotProjectValidationRoadmapPeriod extends Model
{
    protected $table = 'pilot_project_validation_roadmap_periods';

    protected $fillable = [
        'project_id',
        'sort_order',
        'display_current_period',
        'period',
        'phase',
        'status',
        'period_explanation',
        'planned_objective_outcome',
        'pic_update_summary',
        'pic_risks_dependencies',
        'pic_owner',
        'target_date',
        'reviewer_status',
        'period_progress_percent',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'target_date' => 'date',
            'period_progress_percent' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(PilotProjectValidationProject::class, 'project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PilotProjectValidationTimelineTask::class, 'roadmap_period_id')->orderBy('sort_order');
    }
}
