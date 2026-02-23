<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mencatat intervensi per IKK per level (jam ke-1, 2, 3).
     * Jika terintervensi di jam ke-2 maka alert jam ke-3 tidak ditampilkan.
     */
    public function up(): void
    {
        Schema::create('dopm_alert_intervensi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->comment('Tanggal alert');
            $table->string('kode_ikk', 100)->comment('Kode IKK');
            $table->unsignedTinyInteger('alert_level')->comment('Level alert saat di-intervensi: 1, 2, atau 3 (jam ke-1, 2, 3)');
            $table->timestamps();

            $table->unique(['tanggal', 'kode_ikk', 'alert_level'], 'dopm_alert_intervensi_tanggal_ikk_level_unique');
            $table->index(['tanggal', 'kode_ikk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dopm_alert_intervensi');
    }
};
