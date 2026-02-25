<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DopmAlertIntervensiClosure extends Model
{
    protected $table = 'dopm_alert_intervensi_closures';

    protected $fillable = [
        'alert_intervensi_id',
        'closed_by_user_id',
        'closed_by_name',
        'closed_by_email',
        'keterangan',
        'root_cause',
        'tindakan',
        'closed_at',
    ];

    protected $casts = [
        'alert_intervensi_id' => 'integer',
        'closed_by_user_id' => 'integer',
        'closed_at' => 'datetime',
    ];

    /**
     * Relasi ke alert intervensi (issue yang di-close).
     */
    public function alertIntervensi(): BelongsTo
    {
        return $this->belongsTo(DopmAlertIntervensi::class, 'alert_intervensi_id');
    }

    /**
     * Relasi ke user yang melakukan closing.
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    /**
     * Relasi ke evidence (file bukti).
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(DopmAlertIntervensiEvidence::class, 'closure_id');
    }
}
