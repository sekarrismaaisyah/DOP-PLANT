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
        Schema::table('cctv_coverage', function (Blueprint $table) {
            $table->string('kategori_aktivitas')->nullable()->after('coverage_detail_lokasi');
            $table->string('kategori_area')->nullable()->after('kategori_aktivitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cctv_coverage', function (Blueprint $table) {
            $table->dropColumn(['kategori_aktivitas', 'kategori_area']);
        });
    }
};

