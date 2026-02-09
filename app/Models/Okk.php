<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Okk extends Model
{
    use HasFactory;

    protected $table = 'okk';

    protected $guarded = [];

    protected $casts = [
        'ts' => 'datetime',
    ];
}
