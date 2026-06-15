<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hira_improvement_rekayasa_rows', function (Blueprint $table) {
            $table->id();
            $table->string('company', 255)->default('Bukit Makmur');
            $table->unsignedSmallInteger('period_year')->default(2026);
            $table->string('aktivitas', 255)->default('');
            $table->string('site_perusahaan', 255)->default('');
            $table->text('pengendalian_rekayasa')->nullable();
            $table->string('deteksi', 255)->default('');
            $table->string('intervensi', 255)->default('');
            $table->string('prediksi_penurunan_risiko', 128)->default('');
            $table->text('penjelasan_proses_kerja')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['company', 'period_year'], 'idx_hira_rekayasa_company_year');
            $table->index('aktivitas', 'idx_hira_rekayasa_aktivitas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hira_improvement_rekayasa_rows');
    }
};
