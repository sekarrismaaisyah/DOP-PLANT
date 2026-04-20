<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotProjectValidationHistorySnapshot extends Model
{
    protected $table = 'pilot_project_validation_history_snapshots';

    protected $fillable = [
        'project_id',
        'sort_order',
        'snapshot_date',
        'progress',
        'decision_score',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'progress' => 'decimal:2',
            'decision_score' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(PilotProjectValidationProject::class, 'project_id');
    }
}
