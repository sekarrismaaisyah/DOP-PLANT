<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insiden_lpi_layers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insiden_lpi_id');
            $table->string('layer', 100)->nullable();
            $table->string('jenis_item_ipls', 255)->nullable();
            $table->text('detail_layer')->nullable();
            $table->text('keterangan_layer')->nullable();
            $table->timestamps();

            $table->foreign('insiden_lpi_id')->references('id')->on('insiden_lpi')->onDelete('cascade');
            $table->index('insiden_lpi_id');
            $table->index('layer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insiden_lpi_layers');
    }
};
