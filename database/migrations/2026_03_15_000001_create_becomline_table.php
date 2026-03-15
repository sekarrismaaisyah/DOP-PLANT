<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('becomline', function (Blueprint $table) {
            $table->id();
            $table->string('perusahaan_pemilik', 255)->nullable();
            $table->string('site_operasional', 255)->nullable();
            $table->string('jenis_unit_spip', 255)->nullable();
            $table->date('expired')->nullable();
            $table->string('status_permit_spip', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('becomline');
    }
};
