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
        Schema::create('insiden_lpi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insiden_ccr_id')->nullable();
            $table->string('no_kecelakaan', 100)->nullable();
            $table->string('kode_be_investigasi', 100)->nullable();
            $table->string('status_lpi', 50)->nullable();
            $table->date('target_penyelesaian_lpi')->nullable();
            $table->date('actual_penyelesaian_lpi')->nullable();
            $table->string('ketepatan_waktu_lpi', 50)->nullable();
            $table->integer('tanggal')->nullable();
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->integer('minggu_ke')->nullable();
            $table->string('hari', 20)->nullable();
            $table->integer('jam')->nullable();
            $table->integer('menit')->nullable();
            $table->string('shift', 50)->nullable();
            $table->string('perusahaan', 255)->nullable();
            $table->decimal('latitude', 12, 8)->nullable();
            $table->decimal('longitude', 12, 8)->nullable();
            $table->string('departemen', 255)->nullable();
            $table->string('site', 100)->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->string('sublokasi', 255)->nullable();
            $table->string('lokasi_spesifik', 500)->nullable();
            $table->string('lokasi_validasi_hsecm', 255)->nullable();
            $table->string('pja', 500)->nullable();
            $table->string('insiden_dalam_site_mining', 50)->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('injury_status', 50)->nullable();
            $table->text('kronologis')->nullable();
            $table->string('high_potential', 50)->nullable();
            $table->string('alat_terlibat', 255)->nullable();
            $table->string('nama', 255)->nullable();
            $table->string('jabatan', 255)->nullable();
            $table->string('shift_kerja_ke', 50)->nullable();
            $table->integer('hari_kerja_ke')->nullable();
            $table->string('npk', 50)->nullable();
            $table->integer('umur')->nullable();
            $table->string('range_umur', 50)->nullable();
            $table->integer('masa_kerja_perusahaan_tahun')->nullable();
            $table->integer('masa_kerja_perusahaan_bulan')->nullable();
            $table->string('range_masa_kerja_perusahaan', 50)->nullable();
            $table->integer('masa_kerja_bc_tahun')->nullable();
            $table->integer('masa_kerja_bc_bulan')->nullable();
            $table->string('range_masa_kerja_bc', 50)->nullable();
            $table->string('bagian_luka', 255)->nullable();
            $table->decimal('loss_cost', 15, 2)->nullable();
            $table->string('saksi_langsung', 255)->nullable();
            $table->string('atasan_langsung', 255)->nullable();
            $table->string('jabatan_atasan_langsung', 255)->nullable();
            $table->string('kontak', 255)->nullable();
            $table->text('detail_kontak')->nullable();
            $table->string('sumber_kecelakaan', 255)->nullable();
            $table->string('layer', 100)->nullable();
            $table->string('jenis_item_ipls', 255)->nullable();
            $table->text('detail_layer')->nullable();
            $table->text('keterangan_layer')->nullable();
            $table->string('id_lokasi_insiden', 100)->nullable();
            $table->string('id_pja_insiden', 100)->nullable();
            $table->timestamps();

            $table->foreign('insiden_ccr_id')->references('id')->on('insiden_ccr')->onDelete('set null');
            $table->index('insiden_ccr_id');
            $table->index('no_kecelakaan');
            $table->index('site');
            $table->index('kategori');
            $table->index('status_lpi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insiden_lpi');
    }
};
