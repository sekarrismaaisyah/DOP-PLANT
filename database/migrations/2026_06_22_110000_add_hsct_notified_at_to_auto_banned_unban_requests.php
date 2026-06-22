<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auto_banned_unban_requests', function (Blueprint $table) {
            $table->dateTime('hsct_notified_at')->nullable()->after('status');
            $table->index(['status', 'hsct_notified_at'], 'idx_auto_banned_unban_requests_hsct_notify');
        });
    }

    public function down(): void
    {
        Schema::table('auto_banned_unban_requests', function (Blueprint $table) {
            $table->dropIndex('idx_auto_banned_unban_requests_hsct_notify');
            $table->dropColumn('hsct_notified_at');
        });
    }
};
