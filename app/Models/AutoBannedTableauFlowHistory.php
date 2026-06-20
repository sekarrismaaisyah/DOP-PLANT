<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoBannedTableauFlowHistory extends Model
{
    public $timestamps = false;

    protected $table = 'tableau_flow_history';

    protected $fillable = [
        'logged_at',
        'status_code',
        'flow_name',
        'output_name',
        'status_detail',
        'trigger_type',
        'flow_url',
        'created_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
