<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menyimpan snapshot alert DOPM (Need Action / Warning) per jam untuk dashboard IKK.
     * Konsep mirip supervisory_alert_log: simpan per periode (di sini per jam).
     */
    public function up(): void
    {
        Schema::create('dopm_alert_log', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->comment('Tanggal snapshot');
            $table->unsignedTinyInteger('jam')->comment('Jam (0-23) timezone aplikasi');
            $table->unsignedInteger('need_action_count')->default(0)->comment('Jumlah IKK status Merah (Need Action)');
            $table->unsignedInteger('warning_count')->default(0)->comment('Jumlah IKK status Kuning (Warning)');
            $table->json('snapshot')->nullable()->comment('Ringkasan per IKK Merah/Kuning (opsional)');
            $table->timestamps();

            $table->unique(['tanggal', 'jam'], 'dopm_alert_log_tanggal_jam_unique');
            $table->index('tanggal');
            $table->index(['tanggal', 'jam']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dopm_alert_log');
    }
};
