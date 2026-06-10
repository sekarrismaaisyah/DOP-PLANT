<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembatasan_batas_lv_per_lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('site', 255);
            $table->string('lokasi', 255);
            $table->text('detail_lokasi')->nullable();
            $table->unsignedInteger('batas_lv')->default(0);
            $table->timestamps();

            $table->index('site');
            $table->index('lokasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembatasan_batas_lv_per_lokasi');
    }
};
