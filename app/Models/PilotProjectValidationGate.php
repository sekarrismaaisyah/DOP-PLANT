<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PilotProjectValidationGate extends Model
{
    protected $table = 'pilot_project_validation_gates';

    protected $fillable = [
        'project_id',
        'sort_order',
        'gate_label',
        'gate_title',
        'gate_caption',
        'hard_gate',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'hard_gate' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(PilotProjectValidationProject::class, 'project_id');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(PilotProjectValidationMetric::class, 'gate_id')->orderBy('sort_order');
    }
}
