<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Validasi TBC — checklist & tindak lanjut.
     */
    public function up(): void
    {
        Schema::create('validasi_tbc', function (Blueprint $table) {
            $table->id();

            $table->string('validator', 255)->nullable();
            $table->text('tasklist')->nullable();
            $table->text('to_be_concerned_hazard')->nullable();
            $table->string('gr_pspp', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->string('no_item_pspp', 255)->nullable();
            $table->string('kategori_gr', 255)->nullable();
            $table->string('kategori_gr_valid_kpi', 255)->nullable();
            $table->text('blindspot_terlapor_bc')->nullable();
            $table->string('pic_aktual', 500)->nullable();
            $table->text('kronologi_singkat')->nullable();
            $table->text('rootcause_aktual')->nullable();
            $table->text('detail_rootcause_aktual')->nullable();
            $table->text('tindakan_perbaikan_aktual')->nullable();

            $table->timestamps();

            $table->index('validator', 'validasi_tbc_validator_index');
            $table->index('gr_pspp', 'validasi_tbc_gr_pspp_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_tbc');
    }
};
