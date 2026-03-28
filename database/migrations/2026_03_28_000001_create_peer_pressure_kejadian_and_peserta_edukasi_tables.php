<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Peer Pressure — kejadian temuan + edukasi (header) dan peserta (pelanggar/peer).
     */
    public function up(): void
    {
        Schema::create('peer_pressure_kejadian_edukasi', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal_temuan');
            $table->time('jam_temuan');

            $table->string('kelompok_lokasi_temuan', 255);
            $table->string('lokasi_temuan', 255);

            $table->string('kelompok_lokasi_edukasi', 255);
            $table->string('lokasi_edukasi', 255);

            $table->date('tanggal_edukasi');
            $table->time('jam_edukasi');

            $table->string('perusahaan', 255);

            $table->string('tasklist_temuan', 255)->nullable();
            $table->text('kronologi_temuan');
            $table->string('kategori_deviasi', 255);
            $table->string('pemimpin_edukasi', 255);

            $table->string('id_berecord', 64)->nullable();

            $table->string('jenis_kelompok_kerja', 255)->nullable();
            $table->string('kelompok_aktivitas_pekerjaan', 255)->nullable();
            $table->string('aktivitas_pekerjaan', 255)->nullable();
            $table->string('departemen', 100)->nullable();

            $table->text('evidence_url')->nullable();

            $table->unsignedSmallInteger('durasi_edukasi_menit');
            $table->string('status_pelaksanaan_edukasi', 50);

            $table->timestamps();

            $table->index('tanggal_temuan', 'pp_ke_tanggal_temuan_index');
            $table->index('perusahaan', 'pp_ke_perusahaan_index');
            $table->index('status_pelaksanaan_edukasi', 'pp_ke_status_index');
        });

        Schema::create('peer_pressure_peserta_edukasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kejadian_edukasi_id')
                ->constrained('peer_pressure_kejadian_edukasi')
                ->cascadeOnDelete();

            $table->string('sid', 32);
            $table->string('nama', 255);

            $table->enum('peran', ['pelanggar', 'peer']);

            $table->unsignedSmallInteger('urutan')->default(0);

            $table->timestamps();

            $table->index('sid', 'pp_pe_sid_index');
            $table->index(['kejadian_edukasi_id', 'urutan'], 'pp_pe_kejadian_urutan_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peer_pressure_peserta_edukasi');
        Schema::dropIfExists('peer_pressure_kejadian_edukasi');
    }
};
