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
        Schema::create('dop_pengawas_mitra_kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dop_id'); // Foreign key to daily_operation_plans
            $table->string('shift'); // Shift information (e.g., "Shift 1 s/d 2", "Shift 2 s/d 1")
            $table->string('nama_pengawas'); // Name of Pengawas
            $table->string('layer')->nullable(); // Nama layer
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('dop_id')->references('id')->on('daily_operation_plans')->onDelete('cascade');
            
            // Indexes
            $table->index('dop_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dop_pengawas_mitra_kerja');
    }
};

