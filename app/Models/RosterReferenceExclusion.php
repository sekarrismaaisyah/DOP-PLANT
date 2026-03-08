<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RosterReferenceExclusion extends Model
{
    protected $table = 'roster_reference_exclusions';

    protected $fillable = [
        'tanggal',
        'site',
        'roster_table',
        'nama',
        'lokasi',
        'detail_lokasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
