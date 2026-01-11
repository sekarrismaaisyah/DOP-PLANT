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
        Schema::table('intervensi_control_room', function (Blueprint $table) {
            $table->text('resolution')->nullable()->after('issue')->comment('Hasil/resolusi dari issue yang ditangani');
            $table->string('evidence_path')->nullable()->after('resolution')->comment('Path file evidence utama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intervensi_control_room', function (Blueprint $table) {
            $table->dropColumn(['resolution', 'evidence_path']);
        });
    }
};

