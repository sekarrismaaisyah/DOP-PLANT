<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->string('approval_status', 50)
                ->default('waiting_dept_head')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->string('approval_status', 50)
                ->nullable()
                ->default(null)
                ->change();
        });
    }
};