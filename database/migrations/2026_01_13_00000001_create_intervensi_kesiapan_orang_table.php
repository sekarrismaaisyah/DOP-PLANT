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
        Schema::create('intervensi_kesiapan_orang', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi')->nullable(); // Lokasi (bisa dari perusahaan atau area)
            $table->string('area_kerja')->default('Kesiapan Orang'); // Area kerja
            $table->string('nama_pja'); // Nama PJA
            $table->string('tipe_pja')->nullable(); // Tipe PJA
            $table->string('perusahaan')->nullable(); // Perusahaan
            $table->string('id_employee')->nullable(); // ID Employee dari ClickHouse
            $table->string('nama_karyawan')->nullable(); // Nama Karyawan
            $table->string('pic_id')->nullable(); // ID dari ClickHouse user
            $table->string('pic_username')->nullable(); // Username PIC
            $table->string('pic_nama')->nullable(); // Nama PIC
            $table->string('pic_telepon')->nullable(); // Nomor telepon PIC untuk WhatsApp
            $table->text('issue'); // Issue/masalah yang dilaporkan
            $table->text('resolution')->nullable(); // Hasil/resolusi dari issue yang ditangani
            $table->string('evidence_path')->nullable(); // Path file evidence
            $table->string('status')->default('open'); // open, closed
            $table->timestamp('closed_at')->nullable();
            $table->string('closed_by')->nullable();
            $table->string('created_by')->nullable(); // Nama user yang membuat intervensi
            $table->string('created_by_email')->nullable(); // Email user yang membuat
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('lokasi');
            $table->index('area_kerja');
            $table->index('nama_pja');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervensi_kesiapan_orang');
    }
};

