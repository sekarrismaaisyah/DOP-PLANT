<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Index untuk mempercepat query dashboard DOPM (filter by tanggal_dop/timestamp + status).
     */
    public function up(): void
    {
        Schema::table('dopm', function (Blueprint $table) {
            $table->index('tanggal_dop', 'dopm_tanggal_dop_index');
            $table->index('timestamp', 'dopm_timestamp_index');
            $table->index(['tanggal_dop', 'status'], 'dopm_tanggal_dop_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dopm', function (Blueprint $table) {
            $table->dropIndex('dopm_tanggal_dop_index');
            $table->dropIndex('dopm_timestamp_index');
            $table->dropIndex('dopm_tanggal_dop_status_index');
        });
    }
};
