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
        if (Schema::hasTable('dopm')) {
            return;
        }

        Schema::create('dopm', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp')->nullable();
            $table->string('site_ijin_kerja_khusus')->nullable();
            $table->string('perusahaan_ijin_kerja_khusus')->nullable();
            $table->string('jenis_ijin_kerja_khusus')->nullable();
            $table->string('kode_ikk')->nullable();
            $table->date('tanggal_selesai_ijin')->nullable();
            $table->string('nama_pekerjaan')->nullable();
            $table->date('tanggal_dop')->nullable();
            $table->string('status_pengiriman_notif')->nullable();
            $table->string('status')->nullable();
            $table->text('deskripsi_atau_alasan_cancel')->nullable();
            $table->string('sid_layer_2')->nullable();
            $table->string('nama_layer_2')->nullable();
            $table->string('sid_layer_3')->nullable();
            $table->string('nama_layer_3')->nullable();
            $table->string('sid_layer_4')->nullable();
            $table->string('nama_layer_4')->nullable();
            $table->string('jenis_pengawasan_layer')->nullable();
            $table->text('detail_lokasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dopm');
    }
};
