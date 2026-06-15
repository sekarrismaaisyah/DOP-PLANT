<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrAutoBannedTbcSap extends Model
{
    protected $table = 'scr_auto_banned_tbc_sap';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'scraped_at' => 'datetime',
    ];
}
