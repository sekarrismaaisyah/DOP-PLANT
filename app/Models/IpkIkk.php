<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpkIkk extends Model
{
    use HasFactory;

    protected $table = 'ipk_ikk';

    /**
     * All columns are mass assignable (table has many columns).
     */
    protected $guarded = [];

    protected $casts = [
        'ts' => 'datetime',
    ];
}
