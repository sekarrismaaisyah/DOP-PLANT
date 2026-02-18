<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom untuk menyimpan hasil kalkulasi TARP (rekomendasi, daftar CCTV, daftar SAP).
     */
    public function up(): void
    {
        Schema::table('supervisory_alert_log', function (Blueprint $table) {
            $table->json('tarp_recommendations')->nullable()->after('is_high_risk_area')->comment('Rekomendasi TARP [{priority, action}]');
            $table->json('cctv_list')->nullable()->after('tarp_recommendations')->comment('Daftar CCTV di area [{no_cctv, nama_cctv, kondisi, lokasi}]');
            $table->json('sap_list')->nullable()->after('cctv_list')->comment('Daftar SAP hari ini di area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supervisory_alert_log', function (Blueprint $table) {
            $table->dropColumn(['tarp_recommendations', 'cctv_list', 'sap_list']);
        });
    }
};
