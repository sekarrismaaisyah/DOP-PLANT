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
}


