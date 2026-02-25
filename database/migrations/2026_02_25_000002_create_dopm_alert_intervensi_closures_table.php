<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk menyimpan data closing issue dari dopm_alert_intervensi.
     * PIC harus menutup issue dengan memberikan keterangan dan evidence.
     */
    public function up(): void
    {
        Schema::create('dopm_alert_intervensi_closures', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('alert_intervensi_id')
                ->comment('FK ke dopm_alert_intervensi');

            $table->unsignedBigInteger('closed_by_user_id')
                ->comment('ID user yang menutup issue');
            $table->string('closed_by_name', 191)
                ->comment('Nama user yang menutup');
            $table->string('closed_by_email', 191)
                ->nullable()
                ->comment('Email user yang menutup');

            $table->text('keterangan')
                ->nullable()
                ->comment('Catatan/penjelasan penyelesaian issue');

            $table->text('root_cause')
                ->nullable()
                ->comment('Penyebab akar masalah');

            $table->text('tindakan')
                ->nullable()
                ->comment('Tindakan/solusi yang dilakukan');

            $table->timestamp('closed_at')
                ->useCurrent()
                ->comment('Waktu issue di-close');

            $table->timestamps();

            $table->foreign('alert_intervensi_id')
                ->references('id')
                ->on('dopm_alert_intervensi')
                ->onDelete('cascade');

            $table->index('alert_intervensi_id', 'dopm_closures_alert_intervensi_id_index');
            $table->index('closed_by_user_id', 'dopm_closures_closed_by_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dopm_alert_intervensi_closures');
    }
};
