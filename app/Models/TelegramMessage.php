<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'update_id',
        'message_id',
        'chat_id',
        'chat_type',
        'username',
        'first_name',
        'last_name',
        'text',
        'is_from_bot',
        'bot_id',
        'raw_payload',
        'message_date',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'message_date' => 'datetime',
    ];

    /**
     * Check if this message is a CCTV offline alert
     */
    public function isCctvAlert(): bool
    {
        return !empty($this->text) && 
               preg_match('/📡\s*ALERT\s+CCTV\s+OFFLINE/i', $this->text) === 1;
    }

    /**
     * Get parsed CCTV alert data
     */
    public function getCctvAlertData(): ?array
    {
        if (!$this->isCctvAlert()) {
            return null;
        }

        return $this->raw_payload['cctv_alert_parsed'] ?? null;
    }

    /**
     * Get CCTV alert site
     */
    public function getCctvAlertSite(): ?string
    {
        $data = $this->getCctvAlertData();
        return $data['site'] ?? null;
    }

    /**
     * Get CCTV alert units
     */
    public function getCctvAlertUnits(): array
    {
        $data = $this->getCctvAlertData();
        return $data['units'] ?? [];
    }

    /**
     * Scope to filter CCTV alert messages
     */
    public function scopeCctvAlerts($query)
    {
        return $query->where('text', 'like', '%📡 ALERT CCTV OFFLINE%');
    }
}


