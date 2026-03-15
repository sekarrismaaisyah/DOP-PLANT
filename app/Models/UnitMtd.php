<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitMtd extends Model
{
    use HasFactory;

    protected $table = 'konsumsi_bbm_unit';

    protected $fillable = [
        'site',
        'perusahaan',
        'kategori',
        'no_unit',
        'mtd',
        'avg_per_day',
    ];

    protected $casts = [
        'mtd' => 'decimal:2',
        'avg_per_day' => 'decimal:2',
    ];
}
