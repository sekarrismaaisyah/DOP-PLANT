<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FatigueManagementEvaluationStatus;
use App\Enums\FatigueManagementEvidenceStatus;
use Illuminate\Database\Eloquent\Model;

class FatigueManagementProgramMonitoring extends Model
{
    protected $table = 'fatigue_management_program_monitoring';

    protected $fillable = [
        'program_key',
        'partner_key',
        'year',
        'iso_week',
        'evidence_status',
        'evidence_file_path',
        'evidence_original_name',
        'evidence_notes',
        'evidence_uploaded_at',
        'evaluation_status',
        'evaluation_score',
        'evaluation_notes',
        'evaluated_by',
        'evaluated_at',
        'pic_name',
    ];

    protected $casts = [
        'year' => 'integer',
        'evaluation_score' => 'integer',
        'evidence_uploaded_at' => 'datetime',
        'evaluated_at' => 'datetime',
        'evidence_status' => FatigueManagementEvidenceStatus::class,
        'evaluation_status' => FatigueManagementEvaluationStatus::class,
    ];
}
