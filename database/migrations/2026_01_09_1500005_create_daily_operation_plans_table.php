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
        Schema::create('daily_operation_plans', function (Blueprint $table) {
            $table->id();
            $table->string('pekerjaan'); // Pekerjaan/Job description
            $table->string('foto_pekerjaan')->nullable(); // Path to uploaded work photo
            $table->string('unit_id'); // Unit ID
            $table->string('lokasi'); // Location
            $table->text('detail_lokasi')->nullable(); // Location detail
            $table->text('cctv_yang_mengcover')->nullable(); // CCTV that covers the area
            $table->text('potensi_resiko')->nullable(); // Potential risks (can be multiple, stored as text)
            $table->text('pengendalian_bahaya')->nullable(); // Hazard control measures (can be multiple, stored as text)
            $table->text('catatan')->nullable(); // Notes
            $table->date('tanggal'); // Date for the DOP
            $table->timestamps();
            
            // Indexes for performance
            $table->index('tanggal');
            $table->index('unit_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_operation_plans');
    }
};

