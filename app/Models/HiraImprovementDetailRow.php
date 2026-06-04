<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HiraImprovementDetailRow extends Model
{
    protected $table = 'hira_improvement_detail_rows';

    protected $fillable = [
        'company',
        'period_year',
        'improvement_plan',
        'section',
        'activity',
        'sub_activity',
        'sub_sub_activity',
        'rnr',
        'site',
        'faktor',
        'hazard',
        'event_potential',
        'kep_awal',
        'konseq_awal',
        'tp_awal',
        'existing_control',
        'owner_existing',
        'control_level',
        'exposure_type',
        'exposure_before_value',
        'exposure_control_value',
        'kep_sisa',
        'konseq_sisa',
        'target_risk',
        'tp_lanjutan',
        'owner_lanjutan',
        'start_date',
        'target_date',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'kep_awal' => 'integer',
        'konseq_awal' => 'integer',
        'kep_sisa' => 'integer',
        'konseq_sisa' => 'integer',
        'exposure_before_value' => 'float',
        'exposure_control_value' => 'float',
        'start_date' => 'date',
        'target_date' => 'date',
        'sort_order' => 'integer',
    ];
}
