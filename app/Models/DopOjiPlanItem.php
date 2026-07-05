<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DopOjiPlanItem extends Model
{
    protected $table = 'dop_oji_plan_items';

    protected $fillable = [
        'dop_oji_plan_id',
        'dop_safety_plan_item_id',
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
        'evidence_1',
        'evidence_2',
        'evidence_3',
        'evidence_4',
        'section_head',
        'section_head_sid',
        'she_leader',
        'she_leader_sid',
        'dept_head',
        'dept_head_sid',
        'pja_bc',
        'approval_status',
        'approved_at',
        'reject_reason',
       
    ];

    protected function casts(): array
    {
        return [
            'item_no' => 'integer',
            'tools' => 'array',
            'workers' => 'array',
             'approved_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            DopOjiPlan::class,
            'dop_oji_plan_id'
        );
    }

    public function safetyPlanItem()
    {
        return $this->belongsTo(DopSafetyPlanItem::class, 'dop_safety_plan_item_id');
    }


    public function mekanikWorkers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DopOjiPlanItemWorker::class, 'dop_oji_plan_item_id', 'id');
    }
}