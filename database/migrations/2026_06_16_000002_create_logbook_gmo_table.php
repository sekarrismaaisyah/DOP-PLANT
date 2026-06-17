<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('logbook_gmo')) {
            return;
        }

        Schema::create('logbook_gmo', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->time('jam')->nullable();
            $table->string('shift', 50)->index();
            $table->string('perusahan', 255)->index();
            $table->string('nama_karyawan', 255)->index();
            $table->string('sid_karyawan', 100)->nullable()->index();
            $table->string('nama_pengawas', 255)->nullable();
            $table->string('sid_pengawas_pemberi_izin', 100)->nullable();
            $table->text('alasan')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('verifikasi_izin')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->index(['tanggal', 'shift'], 'idx_plv_logbook_gmo_tanggal_shift');
            $table->index(['tanggal', 'perusahan'], 'idx_plv_logbook_gmo_tanggal_perusahan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_gmo');
    }
};
