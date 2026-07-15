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
        Schema::table('dop_oji_plans', function (Blueprint $table) {
            // Tambahkan kolom company dan department setelah kolom site
            $table->string('company', 100)->nullable()->after('site');
            $table->string('department', 100)->nullable()->after('company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dop_oji_plans', function (Blueprint $table) {
            //
        });
    }
};
