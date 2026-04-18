<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speak Up Fatigue — pencatatan per baris (site, perusahaan, SID, nama, tanggal, waktu).
     */
    public function up(): void
    {
        Schema::create('speak_up_fatigue', function (Blueprint $table) {
            $table->id();

            $table->string('site', 255)->nullable();
            $table->string('perusahaan', 255)->nullable();
            $table->string('sid', 32);
            $table->string('nama', 255);

            $table->date('tanggal');
            $table->time('waktu');

            $table->timestamps();

            $table->index('site', 'speak_up_fatigue_site_index');
            $table->index('perusahaan', 'speak_up_fatigue_perusahaan_index');
            $table->index('sid', 'speak_up_fatigue_sid_index');
            $table->index('tanggal', 'speak_up_fatigue_tanggal_index');
            $table->index(['tanggal', 'sid'], 'speak_up_fatigue_tanggal_sid_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speak_up_fatigue');
    }
};
