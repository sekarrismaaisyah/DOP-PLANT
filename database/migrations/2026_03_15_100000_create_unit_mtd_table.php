<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsumsi_bbm_unit', function (Blueprint $table) {
            $table->id();
            $table->string('site', 255)->nullable();
            $table->string('perusahaan', 255)->nullable();
            $table->string('kategori', 255)->nullable();
            $table->string('no_unit', 100)->nullable();
            $table->decimal('mtd', 18, 2)->nullable();
            $table->decimal('avg_per_day', 18, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsumsi_bbm_unit');
    }
};
