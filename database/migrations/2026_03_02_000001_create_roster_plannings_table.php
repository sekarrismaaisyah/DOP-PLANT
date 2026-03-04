<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roster_plannings', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('shift', 50)->nullable();
            $table->string('kategori_area', 255)->nullable();
            $table->enum('source_type', ['IKK', 'DOP']);
            $table->string('source_id', 100);
            $table->string('no_ikk', 100)->nullable();
            $table->string('aktivitas', 255)->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->string('detail_lokasi', 255)->nullable();
            $table->string('id_detail_lokasi', 100)->nullable();
            $table->string('pengawas_langsung', 255)->nullable();
            $table->string('perusahaan_pic', 255)->nullable();
            $table->string('jenis_sap', 100)->nullable();
            $table->enum('status', ['draft', 'assigned', 'completed'])->default('draft');
            $table->timestamps();

            $table->index('tanggal');
            $table->index('source_type');
            $table->index('source_id');
            $table->index('status');
            $table->unique(['source_type', 'source_id', 'tanggal'], 'roster_unique_source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roster_plannings');
    }
};
