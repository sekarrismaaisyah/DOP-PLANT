<?php

namespace App\Models;

use Carbon\Carbon;
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
        $status = strtolower((string) $this->status);
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

        if ($now->between($start, $end)) {
            return 'Open';
        }

        if ($now->gt($end)) {
            return 'Expired';
        }

        return 'Draft';
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
