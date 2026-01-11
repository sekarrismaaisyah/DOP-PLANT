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
        // Add layer column to dop_pengawas_mitra_kerja table if it doesn't exist
        if (!Schema::hasColumn('dop_pengawas_mitra_kerja', 'layer')) {
            Schema::table('dop_pengawas_mitra_kerja', function (Blueprint $table) {
                $table->string('layer')->nullable()->after('nama_pengawas');
            });
        }

        // Add layer column to dop_pic_berau_coal table if it doesn't exist
        if (!Schema::hasColumn('dop_pic_berau_coal', 'layer')) {
            Schema::table('dop_pic_berau_coal', function (Blueprint $table) {
                $table->string('layer')->nullable()->after('nama_pic');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove layer column from dop_pengawas_mitra_kerja table if it exists
        if (Schema::hasColumn('dop_pengawas_mitra_kerja', 'layer')) {
            Schema::table('dop_pengawas_mitra_kerja', function (Blueprint $table) {
                $table->dropColumn('layer');
            });
        }

        // Remove layer column from dop_pic_berau_coal table if it exists
        if (Schema::hasColumn('dop_pic_berau_coal', 'layer')) {
            Schema::table('dop_pic_berau_coal', function (Blueprint $table) {
                $table->dropColumn('layer');
            });
        }
    }
};

