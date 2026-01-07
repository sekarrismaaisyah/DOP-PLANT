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
        Schema::create('intervensi_control_room', function (Blueprint $table) {
            $table->id();
            $table->string('control_room');
            $table->string('pic_id')->nullable(); // ID dari ClickHouse user
            $table->string('pic_username')->nullable(); // Username PIC
            $table->string('pic_nama')->nullable(); // Nama PIC
            $table->string('pic_telepon')->nullable(); // Nomor telepon PIC untuk WhatsApp
            $table->text('issue'); // Issue/masalah yang dilaporkan
            $table->string('created_by')->nullable(); // Nama user yang membuat intervensi
            $table->string('created_by_email')->nullable(); // Email user yang membuat
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('control_room');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervensi_control_room');
    }
};

