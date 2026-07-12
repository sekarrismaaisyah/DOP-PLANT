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
        return $this->hasMany(DopSafetyPlanItem::class)
            ->orderBy('id'); // ❗ ganti dari item_no
    }

    /**
     * OPTIONAL: kalau ada OJI plan 1:1 dengan DOP plan
     */
    public function ojiPlan(): HasMany
    {
        return $this->hasMany(DopOjiPlan::class, 'site', 'site')
            ->whereColumn('plan_date', 'plan_date')
            ->whereColumn('shift', 'shift');
    }

    public function shiftLabel(): string
    {
        return config('dop_safety.shifts')[$this->shift]
            ?? 'Shift ' . $this->shift;
    }

    public function safetyPlanItem()
{
    return $this->belongsTo(DopSafetyPlanItem::class, 'dop_safety_plan_item_id');
}


    public function syncStatusToDone(): void
    {
        $totalItems = $this->items()->count();

        if ($totalItems === 0) {
            return; 
        }

        $doneItems = $this->items()->where('approval_status', 'done')->count();

        if ($totalItems === $doneItems) {
            // Gunakan Enum yang sama seperti sebelumnya
            $this->update(['status' => \App\Enums\DopSafetyPlanStatus::Approved]); 
        }
    }
}