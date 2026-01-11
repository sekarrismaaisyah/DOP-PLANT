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
        Schema::create('dop_cctv', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dop_id'); // Foreign key to daily_operation_plans
            $table->unsignedBigInteger('cctv_id'); // Foreign key to cctv_data_bmo2
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('dop_id')->references('id')->on('daily_operation_plans')->onDelete('cascade');
            $table->foreign('cctv_id')->references('id')->on('cctv_data_bmo2')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['dop_id', 'cctv_id']);
            
            // Indexes
            $table->index('dop_id');
            $table->index('cctv_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dop_cctv');
    }
};

