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
        // Check if columns already exist before adding
        if (!Schema::hasColumn('intervensi_control_room_cctv', 'resolution')) {
            Schema::table('intervensi_control_room_cctv', function (Blueprint $table) {
                $table->text('resolution')->nullable()->after('status_done')->comment('Resolusi untuk CCTV ini');
            });
        }
        
        if (!Schema::hasColumn('intervensi_control_room_cctv', 'evidence_path')) {
            Schema::table('intervensi_control_room_cctv', function (Blueprint $table) {
                $table->string('evidence_path')->nullable()->after('resolution')->comment('Path file evidence untuk CCTV ini');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intervensi_control_room_cctv', function (Blueprint $table) {
            if (Schema::hasColumn('intervensi_control_room_cctv', 'resolution')) {
                $table->dropColumn('resolution');
            }
            if (Schema::hasColumn('intervensi_control_room_cctv', 'evidence_path')) {
                $table->dropColumn('evidence_path');
            }
        });
    }
};

