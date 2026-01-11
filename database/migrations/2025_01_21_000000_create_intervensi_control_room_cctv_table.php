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
        Schema::create('intervensi_control_room_cctv', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('intervensi_id');
            $table->unsignedBigInteger('cctv_id');
            $table->string('status_done', 20)->default('belum')->comment('Status: belum, sudah');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('intervensi_id')->references('id')->on('intervensi_control_room')->onDelete('cascade');
            $table->foreign('cctv_id')->references('id')->on('cctv_data_bmo2')->onDelete('cascade');
            
            // Index untuk performa query
            $table->index('intervensi_id');
            $table->index('cctv_id');
            $table->index('status_done');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['intervensi_id', 'cctv_id'], 'unique_intervensi_cctv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervensi_control_room_cctv');
    }
};

