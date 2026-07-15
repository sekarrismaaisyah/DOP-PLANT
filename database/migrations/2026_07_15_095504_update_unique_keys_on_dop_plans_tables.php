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
        // 1. Update Kunci Unik untuk Tabel DOP Safety
        Schema::table('dop_safety_plans', function (Blueprint $table) {
            // Hapus aturan unik yang lama (hanya 3 kolom)
            $table->dropUnique('idx_dop_safety_plans_site_date_shift');
            
            // Buat aturan unik baru (5 kolom)
            $table->unique(
                ['site', 'company', 'department', 'plan_date', 'shift'], 
                'idx_dop_safety_composite_unique'
            );
        });

        // 2. Update Kunci Unik untuk Tabel DOP OJI
        Schema::table('dop_oji_plans', function (Blueprint $table) {
            // Hapus aturan unik yang lama
            $table->dropUnique('idx_dop_oji_plans_site_date_shift');
            
            // Buat aturan unik baru (5 kolom)
            $table->unique(
                ['site', 'company', 'department', 'plan_date', 'shift'], 
                'idx_dop_oji_composite_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
