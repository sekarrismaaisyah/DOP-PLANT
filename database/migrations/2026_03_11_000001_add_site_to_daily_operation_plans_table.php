<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_operation_plans', function (Blueprint $table) {
            $table->string('site', 255)->nullable()->after('lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('daily_operation_plans', function (Blueprint $table) {
            $table->dropColumn('site');
        });
    }
};
