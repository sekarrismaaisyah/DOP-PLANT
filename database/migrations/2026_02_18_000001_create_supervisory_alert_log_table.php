<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel untuk menyimpan status alert Supervisory (layer Pengawasan Berjarak) per area kerja per hari.
     * Hanya menyimpan ketika risk_level HIGH atau MEDIUM; status hijau (NORMAL) tidak disimpan.
     */
    public function up(): void
    {
        Schema::create('supervisory_alert_log', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->comment('Tanggal status alert');
            $table->string('id_lokasi')->nullable()->comment('ID lokasi/area kerja dari sumber data');
            $table->string('nama_lokasi')->comment('Nama lokasi/area kerja');
            $table->string('risk_level', 20)->comment('HIGH=merah, MEDIUM=kuning, NORMAL=hijau');
            $table->boolean('has_sap_report')->default(false)->comment('Ada laporan SAP dari SO PJA CCTV hari ini');
            $table->boolean('has_online_cctv')->default(false)->comment('CCTV di area dalam kondisi online');
            $table->boolean('is_high_risk_area')->default(false)->comment('Area termasuk zona high risk');
            $table->timestamps();

            $table->unique(['tanggal', 'nama_lokasi'], 'supervisory_alert_log_tanggal_nama_lokasi_unique');
            $table->index('tanggal');
            $table->index('risk_level');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisory_alert_log');
    }
};
