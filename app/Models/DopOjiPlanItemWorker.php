<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DopOjiPlanItemWorker extends Model
{
    protected $table = 'dop_oji_plan_item_workers';

    protected $fillable = [
        'dop_oji_plan_item_id',
        'nrp',
        'name',
        'position',
    ];

    public function ojiPlanItem(): BelongsTo
    {
        return $this->belongsTo(DopOjiPlanItem::class, 'dop_oji_plan_item_id', 'id');
    }
}