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
        if (!Schema::hasTable('telegram_messages')) {
            // If table doesn't exist, the create migration will handle it
            return;
        }

        // Add update_id column if it doesn't exist
        if (!Schema::hasColumn('telegram_messages', 'update_id')) {
            Schema::table('telegram_messages', function (Blueprint $table) {
                $table->unsignedBigInteger('update_id')->nullable()->after('id');
            });
        }

        // Rename message_text to text if message_text exists and text doesn't
        if (Schema::hasColumn('telegram_messages', 'message_text') && !Schema::hasColumn('telegram_messages', 'text')) {
            DB::statement('ALTER TABLE `telegram_messages` CHANGE `message_text` `text` TEXT NULL');
        }

        // Add other columns if they don't exist
        Schema::table('telegram_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('telegram_messages', 'chat_type')) {
                $table->string('chat_type')->nullable()->after('chat_id');
            }

            if (!Schema::hasColumn('telegram_messages', 'username')) {
                $table->string('username')->nullable()->after('chat_type');
            }

            if (!Schema::hasColumn('telegram_messages', 'first_name')) {
                $table->string('first_name')->nullable()->after('username');
            }

            if (!Schema::hasColumn('telegram_messages', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('telegram_messages', 'text')) {
                $table->text('text')->nullable()->after('last_name');
            }

            if (!Schema::hasColumn('telegram_messages', 'is_from_bot')) {
                $table->boolean('is_from_bot')->default(false)->after('text');
            }

            if (!Schema::hasColumn('telegram_messages', 'bot_id')) {
                $table->unsignedBigInteger('bot_id')->nullable()->after('is_from_bot');
            }

            if (!Schema::hasColumn('telegram_messages', 'raw_payload')) {
                $table->longText('raw_payload')->nullable()->after('bot_id');
            }

            if (!Schema::hasColumn('telegram_messages', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }

            // Keep old columns for backward compatibility (chat_title, sender_name)
            // They can be removed later if not needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns in down() to preserve data
        // If you need to rollback, create a separate migration
    }
};

