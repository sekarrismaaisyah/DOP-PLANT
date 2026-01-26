<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HseAiValidation extends Model
{
    use HasFactory;

    protected $table = 'hse_ai_validations';

    protected $fillable = [
        // Original data from ClickHouse
        'task_number',
        'jenis_laporan',
        'aktivitas_pekerjaan',
        'lokasi',
        'detail_lokasi',
        'keterangan',
        'tanggal_pelaporan',
        'perusahaan_pelapor',
        'pelapor',
        'sid_pelapor',
        'jabatan_fungsional_pelapor',
        'departemen_pelapor',
        'pic',
        'sid_pic',
        'jabatan_fungsional_pic',
        'perusahaan_pic',
        'departemen_pic',
        'uri_foto',
        'tools_pengawasan',
        'catatan_tindakan',
        'nik_pelapor',
        'nama_pelapor',
        'nama_perusahaan_pelapor_karyawan',
        'jabatan_fungsional_karyawan_pelapor',
        'latitude',
        'longitude',
        'site',
        'keterangan_lokasi',
        'jam',
        'menit',
        'nama_lokasi',
        'nama_detail_lokasi',
        
        // AI Validation Results
        'ai_match_found',
        'ai_main_category',
        'ai_sub_category',
        'ai_tbc',
        'ai_pspp',
        'ai_gr',
        'ai_incident',
        'ai_justification',
        'ai_confidence_score',
        
        // Metadata
        'validation_date',
        'validated_by',
    ];

    protected $casts = [
        'tanggal_pelaporan' => 'datetime',
        'validation_date' => 'date',
        'ai_match_found' => 'boolean',
        'ai_tbc' => 'boolean',
        'ai_pspp' => 'boolean',
        'ai_gr' => 'boolean',
        'ai_incident' => 'boolean',
        'ai_confidence_score' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user who validated this record
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}

