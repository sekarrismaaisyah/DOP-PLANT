<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::dropIfExists('dop_oji_plan_item_workers');

        Schema::create('dop_oji_plan_item_workers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('dop_oji_plan_item_id')
                  ->constrained('dop_oji_plan_items')
                  ->onDelete('cascade');
            
            $table->text('nrp');
            $table->text('name');
            $table->text('position');
            
            $table->timestamps();

            $table->index('dop_oji_plan_item_id', 'idx_oji_item_workers_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('dop_oji_plan_item_workers');
    }
};