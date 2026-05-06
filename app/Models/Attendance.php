<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'employee_id',
        'kode_sid',
        'nama_snapshot',
        'perusahaan_snapshot',
        'jabatan_struktural_snapshot',
        'jabatan_fungsional_snapshot',
        'attended_at',
        'input_method',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
