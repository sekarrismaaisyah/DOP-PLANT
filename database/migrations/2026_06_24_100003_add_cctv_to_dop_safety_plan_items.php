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

        if (Schema::hasColumn('dop_safety_plan_items', 'cctv')) {
            return;
        }

        Schema::table('dop_safety_plan_items', function (Blueprint $table) {
            $table->string('cctv', 100)->nullable()->after('workers');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('dop_safety_plan_items')) {
            return;
        }

        if (! Schema::hasColumn('dop_safety_plan_items', 'cctv')) {
            return;
        }

        Schema::table('dop_safety_plan_items', function (Blueprint $table) {
            $table->dropColumn('cctv');
        });
    }
};
