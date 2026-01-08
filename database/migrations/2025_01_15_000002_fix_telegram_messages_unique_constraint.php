<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('telegram_messages', function (Blueprint $table) {
            // Remove unique constraint from update_id (make it nullable for bot messages)
            $table->unsignedBigInteger('update_id')->nullable()->change();
            
            // Add unique index for bot messages (message_id + chat_id + is_from_bot)
            // This prevents duplicate bot messages
            $table->unique(['message_id', 'chat_id', 'is_from_bot'], 'telegram_messages_bot_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telegram_messages', function (Blueprint $table) {
            $table->dropUnique('telegram_messages_bot_unique');
            $table->unsignedBigInteger('update_id')->nullable(false)->unique()->change();
        });
    }
};

