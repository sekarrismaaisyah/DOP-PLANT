<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menyimpan Alert 1, 2, 3 per IKK per tanggal: jam ke-1/2/3 sejak mulai IKK,
     * ketika belum ada IPK, satu baris per (tanggal, kode_ikk, alert_level).
     */
    public function up(): void
    {
        Schema::create('dopm_alert_per_ikk', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->comment('Tanggal alert');
            $table->string('kode_ikk', 100)->comment('Kode IKK');
            $table->unsignedTinyInteger('alert_level')->comment('1=jam ke-1, 2=jam ke-2, 3=jam ke-3 sejak mulai IKK');
            $table->unsignedTinyInteger('jam_cek')->nullable()->comment('Jam (0-23) saat pengecekan WITA');
            $table->json('ikk_snapshot')->nullable()->comment('Data IKK saat disimpan (untuk tampilan)');
            $table->timestamps();

            $table->unique(['tanggal', 'kode_ikk', 'alert_level'], 'dopm_alert_per_ikk_tanggal_ikk_level_unique');
            $table->index(['tanggal', 'kode_ikk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dopm_alert_per_ikk');
    }
};
