<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fatigue_management_program_monitoring', function (Blueprint $table) {
            $table->string('frequency_slot', 32)->default('default')->after('iso_week');
        });

        Schema::table('fatigue_management_program_monitoring', function (Blueprint $table) {
            $table->dropUnique('fatigue_mgmt_monitoring_unique_period');
        });

        DB::table('fatigue_management_program_monitoring')
            ->where('frequency_slot', '')
            ->orWhereNull('frequency_slot')
            ->update(['frequency_slot' => 'default']);

        Schema::table('fatigue_management_program_monitoring', function (Blueprint $table) {
            $table->unique(
                ['program_key', 'partner_key', 'year', 'iso_week', 'frequency_slot'],
                'fatigue_mgmt_monitoring_unique_slot'
            );
            $table->index('frequency_slot', 'fatigue_mgmt_monitoring_slot_index');
        });
    }

    public function down(): void
    {
        Schema::table('fatigue_management_program_monitoring', function (Blueprint $table) {
            $table->dropUnique('fatigue_mgmt_monitoring_unique_slot');
            $table->dropIndex('fatigue_mgmt_monitoring_slot_index');
        });

        Schema::table('fatigue_management_program_monitoring', function (Blueprint $table) {
            $table->dropColumn('frequency_slot');
            $table->unique(
                ['program_key', 'partner_key', 'year', 'iso_week'],
                'fatigue_mgmt_monitoring_unique_period'
            );
        });
    }
};
