<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->text('reject_reason')
                ->nullable()
                ->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->dropColumn('reject_reason');
        });
    }
};