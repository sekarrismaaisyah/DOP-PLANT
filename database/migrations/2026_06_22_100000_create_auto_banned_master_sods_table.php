<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_master_sods', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('site', 255);
            $table->string('no_hp', 32);
            $table->timestamps();

            $table->index('site', 'idx_auto_banned_master_sods_site');
            $table->index('nama', 'idx_auto_banned_master_sods_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_master_sods');
    }
};
