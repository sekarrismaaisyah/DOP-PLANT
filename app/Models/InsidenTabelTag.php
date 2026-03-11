<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsidenTabelTag extends Model
{
    protected $table = 'insiden_tabel_tags';

    protected $fillable = [
        'no_kecelakaan',
        'tag',
    ];
}
