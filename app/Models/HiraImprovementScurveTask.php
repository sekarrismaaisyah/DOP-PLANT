<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HiraImprovementScurveTask extends Model
{
    protected $table = 'hira_improvement_scurve_tasks';

    protected $fillable = [
        'company',
        'period_year',
        'improvement_plan',
        'task_name',
        'planned_date',
        'actual_date',
        'status',
        'note',
        'sort_order',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'planned_date' => 'date',
        'actual_date' => 'date',
        'sort_order' => 'integer',
    ];
}
