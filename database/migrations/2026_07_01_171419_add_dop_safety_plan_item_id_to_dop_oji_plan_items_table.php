<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {

            $table->foreignId('dop_safety_plan_item_id')
                ->nullable()
                ->after('dop_oji_plan_id')
                ->constrained('dop_safety_plan_items')
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {

            $table->dropForeign(['dop_safety_plan_item_id']);
            $table->dropColumn('dop_safety_plan_item_id');

        });
    }
};