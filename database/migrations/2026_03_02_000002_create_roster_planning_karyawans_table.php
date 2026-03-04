<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roster_planning_karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roster_planning_id')->constrained('roster_plannings')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_karyawan', 255);
            $table->string('sid_karyawan', 100)->nullable();
            $table->text('task')->nullable();
            $table->text('reason')->nullable();
            $table->text('detail')->nullable();
            $table->timestamps();

            $table->index('roster_planning_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roster_planning_karyawans');
    }
};
