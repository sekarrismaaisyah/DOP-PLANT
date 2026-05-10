<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peer_pressure_kejadian_edukasi', function (Blueprint $table) {
            $table->string('site', 255)->nullable()->after('perusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('peer_pressure_kejadian_edukasi', function (Blueprint $table) {
            $table->dropColumn('site');
        });
    }
};
