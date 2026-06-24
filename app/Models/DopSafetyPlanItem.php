<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DopSafetyPlanItem extends Model
{
    protected $fillable = [
        'dop_safety_plan_id',
        'item_no',
        'section_name',
        'unit_code',
        'unit_category',
        'location',
        'job_detail',
        'work_permit',
        'tools',
        'workers',
        'cctv',
        'group_leader',
        'section_head',
        'she_leader',
        'dept_head',
        'pja_bc',
    ];

    protected function casts(): array
    {
        return [
            'item_no' => 'integer',
            'tools' => 'array',
            'workers' => 'array',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(DopSafetyPlan::class, 'dop_safety_plan_id');
    }
}
