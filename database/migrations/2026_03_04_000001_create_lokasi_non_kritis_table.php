<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_non_kritis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('id_site', 50)->nullable();
            $table->string('site', 255)->nullable();
            $table->string('id_lokasi', 50)->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->string('id_detil_lokasi', 50)->nullable();
            $table->string('detil_lokasi', 255)->nullable();
            $table->string('kategori_area', 20)->default('non_kritis'); // 'kritis' | 'non_kritis'
            $table->timestamps();

            $table->index('tanggal');
            $table->index('kategori_area');
            $table->unique(['tanggal', 'id_site', 'id_lokasi', 'id_detil_lokasi'], 'lokasi_non_kritis_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_non_kritis');
    }
};
