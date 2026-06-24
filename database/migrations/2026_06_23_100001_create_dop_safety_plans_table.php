<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dop_safety_plans', function (Blueprint $table) {
            $table->id();
            $table->string('site', 50)->index();
            $table->date('plan_date')->index();
            $table->unsignedTinyInteger('shift')->index();
            $table->string('status', 30)->default('draft')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('auth_location_date')->nullable();
            $table->string('created_by_name')->nullable();
            $table->string('created_by_position')->nullable();
            $table->string('acknowledged_1_name')->nullable();
            $table->string('acknowledged_1_position')->nullable();
            $table->string('acknowledged_2_name')->nullable();
            $table->string('acknowledged_2_position')->nullable();
            $table->string('acknowledged_3_name')->nullable();
            $table->string('acknowledged_3_position')->nullable();

            $table->timestamps();

            $table->unique(['site', 'plan_date', 'shift'], 'idx_dop_safety_plans_site_date_shift');
            $table->index(['plan_date', 'shift'], 'idx_dop_safety_plans_date_shift');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dop_safety_plans');
    }
};
