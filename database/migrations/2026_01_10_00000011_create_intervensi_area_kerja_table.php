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
        Schema::create('intervensi_area_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi'); // Lokasi area kerja
            $table->string('area_kerja')->nullable(); // Area kerja (optional)
            $table->string('pic_id')->nullable(); // ID dari ClickHouse user
            $table->string('pic_username')->nullable(); // Username PIC
            $table->string('pic_nama')->nullable(); // Nama PIC
            $table->string('pic_telepon')->nullable(); // Nomor telepon PIC untuk WhatsApp
            $table->text('issue'); // Issue/masalah yang dilaporkan
            $table->text('resolution')->nullable(); // Hasil/resolusi dari issue yang ditangani
            $table->string('evidence_path')->nullable(); // Path file evidence
            $table->string('status')->default('open'); // open, closed
            // $table->string('status_done', 20)->default('belum')->comment('Status: belum, sudah');
            $table->timestamp('closed_at')->nullable();
            $table->string('closed_by')->nullable();
            $table->string('created_by')->nullable(); // Nama user yang membuat intervensi
            $table->string('created_by_email')->nullable(); // Email user yang membuat
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('lokasi');
            $table->index('area_kerja');
            $table->index('status');
            // $table->index('status_done');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervensi_area_kerja');
    }
};

