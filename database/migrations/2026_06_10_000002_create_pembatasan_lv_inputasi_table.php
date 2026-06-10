<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembatasan_lv_inputasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('shift');
            $table->string('status', 32);
            $table->string('nama_driver', 255);
            $table->string('driver_ref', 64)->nullable();
            $table->string('no_lambung', 64);
            $table->string('id_unit', 64)->nullable();
            $table->string('lokasi', 255);
            $table->text('detail_lokasi')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('creator_name', 255);
            $table->string('control_room', 255);
            $table->string('aktivitas', 255)->nullable();
            $table->dateTime('checkin_at');
            $table->dateTime('checkout_at')->nullable();
            $table->unsignedBigInteger('checkout_by_id')->nullable();
            $table->string('checkout_by_name', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['control_room', 'checkin_at']);
            $table->index(['control_room', 'checkout_at']);
            $table->index('no_lambung');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembatasan_lv_inputasi');
    }
};
