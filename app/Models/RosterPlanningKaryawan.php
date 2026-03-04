<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterPlanningKaryawan extends Model
{
    use HasFactory;

    protected $table = 'roster_planning_karyawans';

    protected $fillable = [
        'roster_planning_id',
        'user_id',
        'nama_karyawan',
        'sid_karyawan',
        'task',
        'reason',
        'detail',
    ];

    public function rosterPlanning(): BelongsTo
    {
        return $this->belongsTo(RosterPlanning::class, 'roster_planning_id');
    }
}
