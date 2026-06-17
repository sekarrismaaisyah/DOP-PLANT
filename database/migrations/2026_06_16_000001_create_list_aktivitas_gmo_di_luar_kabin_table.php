<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('list_aktivitas_gmo_di_luar_kabin')) {
            return;
        }

        Schema::create('list_aktivitas_gmo_di_luar_kabin', function (Blueprint $table) {
            $table->id();
            $table->string('site', 50)->index();
            $table->string('perusahaan', 255)->index();
            $table->text('kategori_aktivitas_luar_kabin');
            $table->text('detail_aktivitas_luar_kabin');
            $table->string('frekuensi_aktivitas', 100)->nullable();
            $table->text('potensi_risiko')->nullable();
            $table->text('kategori_kontrol_existing')->nullable();
            $table->text('aktual_kontrol_existing')->nullable();
            $table->text('kategori_potensi_kontrol_essr')->nullable();
            $table->text('potensi_kontrol_essr')->nullable();
            $table->text('rencana_pemenuhan')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['site', 'perusahaan'], 'idx_plv_gmo_aktivitas_site_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_aktivitas_gmo_di_luar_kabin');
    }
};
