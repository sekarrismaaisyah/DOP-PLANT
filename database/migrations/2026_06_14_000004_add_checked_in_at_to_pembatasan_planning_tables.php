<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembatasan_lv_planning', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->after('catatan');
            $table->index('checked_in_at', 'idx_plv_lv_planning_checked_in');
        });

        Schema::table('pembatasan_orang_planning', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->after('catatan');
            $table->index('checked_in_at', 'idx_plv_orang_planning_checked_in');
        });
    }

    public function down(): void
    {
        Schema::table('pembatasan_lv_planning', function (Blueprint $table) {
            $table->dropIndex('idx_plv_lv_planning_checked_in');
            $table->dropColumn('checked_in_at');
        });

        Schema::table('pembatasan_orang_planning', function (Blueprint $table) {
            $table->dropIndex('idx_plv_orang_planning_checked_in');
            $table->dropColumn('checked_in_at');
        });
    }
};
