<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DopSafetyPlanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DopSafetyPlan extends Model
{
    protected $fillable = [
        'site',
        'plan_date',
        'shift',
        'status',
        'user_id',
        'auth_location_date',
        'created_by_name',
        'created_by_position',
        'acknowledged_1_name',
        'acknowledged_1_position',
        'acknowledged_2_name',
        'acknowledged_2_position',
        'acknowledged_3_name',
        'acknowledged_3_position',
    ];

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'shift' => 'integer',
            'status' => DopSafetyPlanStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DopSafetyPlanItem::class)->orderBy('item_no');
    }

    public function shiftLabel(): string
    {
        return config('dop_safety.shifts')[$this->shift] ?? 'Shift ' . $this->shift;
    }
}
