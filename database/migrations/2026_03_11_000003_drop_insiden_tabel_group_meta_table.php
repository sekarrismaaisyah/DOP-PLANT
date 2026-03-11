<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hapus tabel group_meta (total_entri_override tidak dipakai lagi).
     */
    public function up(): void
    {
        Schema::dropIfExists('insiden_tabel_group_meta');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('insiden_tabel_group_meta', function (Blueprint $table) {
            $table->id();
            $table->string('no_kecelakaan')->unique();
            $table->timestamps();
        });
    }
};
