<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roster_reference_exclusions', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('site', 100);
            $table->string('roster_table', 64);
            $table->string('nama', 255);
            $table->string('lokasi', 255)->nullable();
            $table->string('detail_lokasi', 255)->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'site', 'roster_table']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roster_reference_exclusions');
    }
};
