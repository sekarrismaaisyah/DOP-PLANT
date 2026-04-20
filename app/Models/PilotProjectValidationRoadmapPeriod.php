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
        'period',
        'phase',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
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
