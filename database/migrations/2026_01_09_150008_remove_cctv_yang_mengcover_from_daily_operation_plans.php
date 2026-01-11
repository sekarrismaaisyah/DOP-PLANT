<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_operation_plans', function (Blueprint $table) {
            $table->dropColumn('cctv_yang_mengcover');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_operation_plans', function (Blueprint $table) {
            $table->text('cctv_yang_mengcover')->nullable()->after('detail_lokasi');
        });
    }
};

