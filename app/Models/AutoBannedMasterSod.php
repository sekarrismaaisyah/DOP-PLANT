<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoBannedMasterSod extends Model
{
    protected $table = 'auto_banned_master_sods';

    protected $fillable = [
        'nama',
        'site',
        'no_hp',
    ];
}
