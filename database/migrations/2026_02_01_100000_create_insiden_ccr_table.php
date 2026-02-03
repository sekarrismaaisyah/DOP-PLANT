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
        Schema::create('insiden_ccr', function (Blueprint $table) {
            $table->id();
            $table->string('ccr_id', 50)->nullable();
            $table->string('no_kecelakaan', 100)->nullable();
            $table->string('ccr_jenis_insiden', 255)->nullable();
            $table->datetime('ccr_waktu_pelaporan')->nullable();
            $table->datetime('ccr_waktu_insiden')->nullable();
            $table->text('ccr_kronologi')->nullable();
            $table->string('ccr_nama_call_taker', 255)->nullable();
            $table->string('ccr_perusahaan_call_taker', 255)->nullable();
            $table->string('ccr_nama_pelapor', 255)->nullable();
            $table->string('ccr_perusahaan_pelapor', 255)->nullable();
            $table->string('ccr_lokasi_perusahaan', 255)->nullable();
            $table->string('ccr_site', 100)->nullable();
            $table->string('ccr_lokasi', 255)->nullable();
            $table->string('ccr_detil_lokasi', 500)->nullable();
            $table->text('ccr_keterangan_lokasi')->nullable();
            $table->string('ccr_status', 100)->nullable();
            $table->string('ccr_pic_investigasi', 255)->nullable();
            $table->string('ccr_pic_investigasi_perusahaan', 255)->nullable();
            $table->text('ket_not_investigasi')->nullable();
            $table->timestamps();
            
            $table->index('ccr_id');
            $table->index('ccr_jenis_insiden');
            $table->index('ccr_site');
            $table->index('ccr_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insiden_ccr');
    }
};
