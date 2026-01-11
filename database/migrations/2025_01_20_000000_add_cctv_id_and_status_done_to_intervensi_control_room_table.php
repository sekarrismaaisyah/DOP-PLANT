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
            $table->unsignedBigInteger('cctv_id')->nullable()->after('control_room');
            $table->string('status_done', 20)->default('belum')->after('status')->comment('Status: belum, sudah');
            
            // Add index for better query performance
            $table->index('cctv_id');
            $table->index('status_done');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intervensi_control_room', function (Blueprint $table) {
            $table->dropIndex(['cctv_id']);
            $table->dropIndex(['status_done']);
            $table->dropColumn(['cctv_id', 'status_done']);
        });
    }
};

