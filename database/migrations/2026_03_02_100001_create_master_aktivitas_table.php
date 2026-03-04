<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aktivitas', 255);
            $table->string('periode_check', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_aktivitas');
    }
};
