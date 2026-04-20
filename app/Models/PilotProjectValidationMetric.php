<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotProjectValidationMetric extends Model
{
    protected $table = 'pilot_project_validation_metrics';

    protected $fillable = [
        'gate_id',
        'sort_order',
        'metric_name',
        'metric_type',
        'metric_desc',
        'direction',
        'unit',
        'critical',
        'metric_value',
        'min_value',
        'max_value',
        'step_value',
        'pass_threshold',
        'conditional_threshold',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'critical' => 'boolean',
            'min_value' => 'decimal:4',
            'max_value' => 'decimal:4',
            'step_value' => 'decimal:4',
            'pass_threshold' => 'decimal:4',
            'conditional_threshold' => 'decimal:4',
        ];
    }

    public function gate(): BelongsTo
    {
        return $this->belongsTo(PilotProjectValidationGate::class, 'gate_id');
    }
}
