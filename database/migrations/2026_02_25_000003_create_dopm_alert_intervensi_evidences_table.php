<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk menyimpan file evidence dari closing issue.
     * Satu closure bisa memiliki banyak evidence (gambar, PDF, dll).
     */
    public function up(): void
    {
        Schema::create('dopm_alert_intervensi_evidences', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('closure_id')
                ->comment('FK ke dopm_alert_intervensi_closures');

            $table->string('file_name', 255)
                ->comment('Nama file asli');
            $table->string('file_path', 500)
                ->comment('Path file di storage');
            $table->string('file_type', 50)
                ->nullable()
                ->comment('MIME type file (image/jpeg, application/pdf, etc)');
            $table->unsignedBigInteger('file_size')
                ->nullable()
                ->comment('Ukuran file dalam bytes');

            $table->string('keterangan', 500)
                ->nullable()
                ->comment('Keterangan/deskripsi evidence');

            $table->unsignedBigInteger('uploaded_by_user_id')
                ->comment('ID user yang upload');

            $table->timestamps();

            $table->index('closure_id', 'dopm_evidences_closure_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dopm_alert_intervensi_evidences');
    }
};
