<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SBS — kelompok (grup) dan anggota.
     */
    public function up(): void
    {
        Schema::create('sbs_kelompok', function (Blueprint $table) {
            $table->id();

            $table->string('site', 255)->nullable();
            $table->string('perusahaan', 255)->nullable();

            $table->string('level_grup', 255);
            $table->string('nama_kelompok', 255);

            $table->string('nama_bapak_asuh', 255);
            $table->string('sid_bapak_asuh', 32);

            $table->timestamps();

            $table->index('site', 'sbs_kelompok_site_index');
            $table->index('perusahaan', 'sbs_kelompok_perusahaan_index');
            $table->index('level_grup', 'sbs_kelompok_level_grup_index');
            $table->index('nama_kelompok', 'sbs_kelompok_nama_kelompok_index');
            $table->index(['perusahaan', 'nama_kelompok'], 'sbs_kelompok_perusahaan_nama_index');
        });

        Schema::create('sbs_anggota', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelompok_id')
                ->constrained('sbs_kelompok')
                ->cascadeOnDelete();

            $table->string('nama', 255);
            $table->string('sid', 32);

            $table->unsignedSmallInteger('urutan')->default(0);

            $table->timestamps();

            $table->index('sid', 'sbs_anggota_sid_index');
            $table->index(['kelompok_id', 'urutan'], 'sbs_anggota_kelompok_urutan_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbs_anggota');
        Schema::dropIfExists('sbs_kelompok');
    }
};
