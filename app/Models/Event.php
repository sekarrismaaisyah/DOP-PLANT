<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_code',
        'qr_token',
        'meeting_type_id',
        'site_id',
        'meeting_level',
        'target_companies',
        'target_positions',
        'target_departments',
        'meeting_date',
        'week',
        'start_time',
        'end_time',
        'status',
        'closed_at',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'closed_at' => 'datetime',
        'target_companies' => 'array',
        'target_positions' => 'array',
        'target_departments' => 'array',
    ];

    protected $appends = [
        'computed_status',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function eventMinute(): HasOne
    {
        return $this->hasOne(EventMinute::class);
    }

    public function getComputedStatusAttribute(): string
    {
        return $this->runtimeStatus();
    }

    public function runtimeStatus(): string
    {
        $status = strtolower((string) $this->status);
        if ($status === 'draft') {
            return 'Draft';
        }
        if ($status === 'closed' || $this->closed_at !== null) {
            return 'Closed';
        }

        $meetingDate = $this->meeting_date instanceof Carbon
            ? $this->meeting_date
            : Carbon::parse($this->meeting_date);

        $start = Carbon::parse($meetingDate->toDateString() . ' ' . $this->start_time);
        $end = Carbon::parse($meetingDate->toDateString() . ' ' . $this->end_time);
        $now = now();

        if ($now->lt($start)) {
            return 'Upcoming';
        }

        if ($now->lte($end)) {
            return 'Open';
        }

        return 'Overrun';
    }

    public function scopeRuntimeActive(Builder $query): Builder
    {
        $now = now();

        return $query->whereNull('closed_at')
            ->whereNotIn('events.status', ['closed', 'draft'])
            ->where(function (Builder $q) use ($now): void {
                $q->whereIn('events.status', ['open', 'overrun'])
                    ->orWhere(function (Builder $open) use ($now): void {
                        $open->whereDate('events.meeting_date', $now->toDateString())
                            ->whereTime('events.start_time', '<=', $now->format('H:i:s'))
                            ->whereTime('events.end_time', '>=', $now->format('H:i:s'));
                    })
                    ->orWhere(function (Builder $over) use ($now): void {
                        $over->whereDate('events.meeting_date', $now->toDateString())
                            ->whereTime('events.end_time', '<', $now->format('H:i:s'));
                    });
            });
    }

    public function scopeRuntimeInactive(Builder $query): Builder
    {
        $now = now();

        return $query->where(function (Builder $q) use ($now): void {
            $q->whereNotNull('closed_at')
                ->orWhereIn('events.status', ['closed', 'draft'])
                ->orWhereDate('events.meeting_date', '<', $now->toDateString())
                ->orWhereDate('events.meeting_date', '>', $now->toDateString())
                ->orWhere(function (Builder $upcomingToday) use ($now): void {
                    $upcomingToday->whereDate('events.meeting_date', $now->toDateString())
                        ->whereTime('events.start_time', '>', $now->format('H:i:s'))
                        ->whereNull('closed_at')
                        ->whereNotIn('events.status', ['open', 'overrun']);
                })
                ->orWhere(function (Builder $pastToday) use ($now): void {
                    $pastToday->whereDate('events.meeting_date', $now->toDateString())
                        ->whereTime('events.end_time', '<', $now->format('H:i:s'))
                        ->whereNull('closed_at')
                        ->whereNotIn('events.status', ['open', 'overrun']);
                });
        });
    }

    public function isOpenForAttendance(): bool
    {
        $computed = $this->computed_status;

        return in_array($computed, ['Open'], true) || strtolower((string) $this->status) === 'overrun';
    }

    public function attendanceRate(): float
    {
        $expectedCompanyIds = Company::query()
            ->whereHas('sites', function ($query): void {
                $query->where('sites.id', $this->site_id)->where('company_site.is_required', true);
            })
            ->pluck('companies.id');

        $expectedCount = $expectedCompanyIds->count();
        if ($expectedCount === 0) {
            return 0.0;
        }

        $attendedCompanies = $this->attendances()
            ->join('employees', 'employees.id', '=', 'attendances.employee_id')
            ->whereIn('employees.company_id', $expectedCompanyIds)
            ->distinct('employees.company_id')
            ->count('employees.company_id');

        return round(($attendedCompanies / $expectedCount) * 100, 2);
    }
}
