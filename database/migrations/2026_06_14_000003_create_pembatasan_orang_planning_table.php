<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembatasan_orang_planning', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_plan');
            $table->unsignedTinyInteger('shift');
            $table->string('status', 32);
            $table->string('sid', 64);
            $table->string('nama', 255);
            $table->string('nik', 64)->nullable();
            $table->string('nama_perusahaan', 255)->nullable();
            $table->string('site', 255)->nullable();
            $table->string('dept', 255)->nullable();
            $table->string('lokasi', 255);
            $table->text('detail_lokasi')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('creator_name', 255);
            $table->string('control_room', 255);
            $table->string('aktivitas', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('tanggal_plan');
            $table->index(['control_room', 'tanggal_plan']);
            $table->index(['tanggal_plan', 'shift']);
            $table->index('sid');
            $table->index('lokasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembatasan_orang_planning');
    }
};
