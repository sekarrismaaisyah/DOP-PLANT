<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PilotProjectValidationProject extends Model
{
    protected $table = 'pilot_project_validation_projects';

    protected $fillable = [
        'project_name',
        'subtitle',
        'pilot_area',
        'support',
        'current_phase',
        'progress',
        'current_period',
        'next_milestone',
        'need_support_pic',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'decimal:2',
        ];
    }

    public function roadmapPeriods(): HasMany
    {
        return $this->hasMany(PilotProjectValidationRoadmapPeriod::class, 'project_id')->orderBy('sort_order');
    }

    public function gates(): HasMany
    {
        return $this->hasMany(PilotProjectValidationGate::class, 'project_id')->orderBy('sort_order');
    }

    public function historySnapshots(): HasMany
    {
        return $this->hasMany(PilotProjectValidationHistorySnapshot::class, 'project_id')->orderBy('sort_order');
    }
}
