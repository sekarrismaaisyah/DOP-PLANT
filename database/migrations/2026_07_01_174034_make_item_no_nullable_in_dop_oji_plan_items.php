<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->integer('item_no')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->integer('item_no')->nullable(false)->change();
        });
    }
};