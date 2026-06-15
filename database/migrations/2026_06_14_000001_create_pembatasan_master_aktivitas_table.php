<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembatasan_master_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->string('site', 255);
            $table->string('perusahaan', 255);
            $table->string('departemen', 255);
            $table->text('kategori_aktivitas_luar_kabin');
            $table->text('detail_aktivitas_pengoperasian_lv');
            $table->unsignedSmallInteger('frekuensi_aktivitas_per_shift')->default(0);
            $table->unsignedSmallInteger('estimasi_jumlah_lv_per_shift')->default(0);
            $table->timestamps();

            $table->index('site');
            $table->index('perusahaan');
            $table->index('departemen');
            $table->index(['site', 'perusahaan', 'departemen'], 'idx_plv_master_aktivitas_site_perusahaan_dept');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembatasan_master_aktivitas');
    }
};
