<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DmsCalibration extends Model
{
    use HasFactory;

    protected $table = 'dms_calibrations';

    protected $fillable = [
        'driver_id',
        'trip_id',
        'calibration_start_time',
        'calibration_end_time',
        't_close',
        'ear_mean',
        'ear_sd',
        'data_points_count',
        'notes',
    ];

    protected $casts = [
        'calibration_start_time' => 'datetime',
        'calibration_end_time' => 'datetime',
        't_close' => 'decimal:6',
        'ear_mean' => 'decimal:6',
        'ear_sd' => 'decimal:6',
        'data_points_count' => 'integer',
    ];
}

