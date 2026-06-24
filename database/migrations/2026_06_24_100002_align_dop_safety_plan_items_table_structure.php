<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dop_safety_plan_items')) {
            return;
        }

        Schema::table('dop_safety_plan_items', function (Blueprint $table) {
            if (Schema::hasColumn('dop_safety_plan_items', 'unit_category')) {
                $table->dropIndex(['unit_category']);
                $table->dropColumn('unit_category');
            }

            if (! Schema::hasColumn('dop_safety_plan_items', 'cctv')) {
                $table->string('cctv', 100)->nullable()->after('workers');
            }

            if (! Schema::hasColumn('dop_safety_plan_items', 'group_leader_sid')) {
                $table->string('group_leader_sid', 50)->nullable()->after('group_leader');
            }

            if (! Schema::hasColumn('dop_safety_plan_items', 'section_head_sid')) {
                $table->string('section_head_sid', 50)->nullable()->after('section_head');
            }

            if (! Schema::hasColumn('dop_safety_plan_items', 'she_leader_sid')) {
                $table->string('she_leader_sid', 50)->nullable()->after('she_leader');
            }

            if (! Schema::hasColumn('dop_safety_plan_items', 'dept_head_sid')) {
                $table->string('dept_head_sid', 50)->nullable()->after('dept_head');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('dop_safety_plan_items')) {
            return;
        }

        Schema::table('dop_safety_plan_items', function (Blueprint $table) {
            foreach (['group_leader_sid', 'section_head_sid', 'she_leader_sid', 'dept_head_sid'] as $column) {
                if (Schema::hasColumn('dop_safety_plan_items', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (! Schema::hasColumn('dop_safety_plan_items', 'unit_category')) {
                $table->string('unit_category', 20)->default('TRACK')->index()->after('unit_code');
            }
        });
    }
};
