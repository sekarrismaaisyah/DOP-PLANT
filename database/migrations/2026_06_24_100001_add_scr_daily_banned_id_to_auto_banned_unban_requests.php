<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('auto_banned_unban_requests')) {
            return;
        }

        Schema::table('auto_banned_unban_requests', function (Blueprint $table): void {
            if (! Schema::hasColumn('auto_banned_unban_requests', 'scr_daily_banned_id')) {
                $table->unsignedBigInteger('scr_daily_banned_id')
                    ->nullable()
                    ->after('id');

                $table->index(
                    'scr_daily_banned_id',
                    'idx_auto_banned_unban_requests_scr_daily'
                );
            }
        });

        if (Schema::hasTable('scr_daily_banned')) {
            Schema::table('auto_banned_unban_requests', function (Blueprint $table): void {
                $table->foreign('scr_daily_banned_id', 'fk_auto_banned_unban_scr_daily')
                    ->references('id')
                    ->on('scr_daily_banned')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('auto_banned_unban_requests')) {
            return;
        }

        Schema::table('auto_banned_unban_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('auto_banned_unban_requests', 'scr_daily_banned_id')) {
                try {
                    $table->dropForeign('fk_auto_banned_unban_scr_daily');
                } catch (\Throwable) {
                    // FK mungkin belum pernah dibuat jika scr_daily_banned tidak ada saat migrate up.
                }

                $table->dropIndex('idx_auto_banned_unban_requests_scr_daily');
                $table->dropColumn('scr_daily_banned_id');
            }
        });
    }
};
