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
        'location',
        'job_detail',
        'work_permit',
        'tools',
        'workers',
        'cctv',
        'group_leader',
        'group_leader_sid',
        'section_head',
        'section_head_sid',
        'she_leader',
        'she_leader_sid',
        'dept_head',
        'dept_head_sid',
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
