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
        Schema::table('dopm', function (Blueprint $table) {
            if (!Schema::hasColumn('dopm', 'sid_layer_1')) {
                $table->string('sid_layer_1')->nullable()->after('deskripsi_atau_alasan_cancel');
            }
            if (!Schema::hasColumn('dopm', 'nama_layer_1')) {
                $table->string('nama_layer_1')->nullable()->after('sid_layer_1');
            }
            if (!Schema::hasColumn('dopm', 'shift')) {
                $table->string('shift')->nullable()->after('nama_layer_1');
            }
            if (!Schema::hasColumn('dopm', 'jam_mulai')) {
                $table->string('jam_mulai')->nullable()->after('shift');
            }
            if (!Schema::hasColumn('dopm', 'jam_akhir')) {
                $table->string('jam_akhir')->nullable()->after('jam_mulai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dopm', function (Blueprint $table) {
            $table->dropColumn(['sid_layer_1', 'nama_layer_1', 'shift', 'jam_mulai', 'jam_akhir']);
        });
    }
};
