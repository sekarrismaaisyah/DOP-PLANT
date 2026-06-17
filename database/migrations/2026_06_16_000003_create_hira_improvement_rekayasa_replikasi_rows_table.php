<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hira_improvement_rekayasa_replikasi_rows', function (Blueprint $table) {
            $table->id();
            $table->string('company', 255)->default('Bukit Makmur');
            $table->unsignedSmallInteger('period_year')->default(2026);
            $table->string('site', 255)->default('');
            $table->string('perusahaan', 255)->default('');
            $table->string('aktivitas', 255)->default('');
            $table->string('kategori_rekayasa', 255)->default('');
            $table->string('origin_replikasi', 255)->default('');
            $table->text('pengendalian_rekayasa')->nullable();
            $table->text('penjelasan_proses_kerja')->nullable();
            $table->string('deteksi', 255)->default('');
            $table->string('intervensi', 255)->default('');
            $table->string('level_efektivitas', 128)->default('');
            $table->string('nilai_risiko_awal', 128)->default('');
            $table->string('prediksi_penurunan_risiko', 128)->default('');
            $table->string('prediksi_risiko_sisa', 128)->default('');
            $table->string('target', 255)->default('');
            $table->string('total_populasi', 128)->default('');
            $table->string('target_replikasi_komitmen', 128)->default('');
            $table->string('aktual_replikasi', 128)->default('');
            $table->string('satuan', 128)->default('');
            $table->string('jumlah_mitra_replikasi', 128)->default('');
            $table->string('tercover_behira', 128)->default('');
            $table->string('potensi_peningkatan_level_efektivitas', 255)->default('');
            $table->text('pengendalian_pen_tingkatan_level_efektivitas')->nullable();
            $table->string('target_standar_isasi_due_date', 128)->default('');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['company', 'period_year'], 'idx_hira_replikasi_company_year');
            $table->index('site', 'idx_hira_replikasi_site');
            $table->index('aktivitas', 'idx_hira_replikasi_aktivitas');
            $table->index('kategori_rekayasa', 'idx_hira_replikasi_kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hira_improvement_rekayasa_replikasi_rows');
    }
};
