<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DopmAlertIntervensiEvidence extends Model
{
    protected $table = 'dopm_alert_intervensi_evidences';

    protected $fillable = [
        'closure_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'keterangan',
        'uploaded_by_user_id',
    ];

    protected $casts = [
        'closure_id' => 'integer',
        'file_size' => 'integer',
        'uploaded_by_user_id' => 'integer',
    ];

    /**
     * Relasi ke closure.
     */
    public function closure(): BelongsTo
    {
        return $this->belongsTo(DopmAlertIntervensiClosure::class, 'closure_id');
    }

    /**
     * Relasi ke user yang upload.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    /**
     * Get URL untuk akses file evidence.
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get ukuran file dalam format readable (KB, MB).
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}
