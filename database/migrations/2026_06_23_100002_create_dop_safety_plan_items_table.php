<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dop_safety_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dop_safety_plan_id')->constrained('dop_safety_plans')->cascadeOnDelete();
            $table->unsignedInteger('item_no')->index();
            $table->string('section_name', 80)->index();
            $table->string('unit_code', 50);
            $table->string('unit_category', 20)->index();
            $table->string('location');
            $table->text('job_detail');
            $table->string('work_permit')->default('N/A');
            $table->json('tools')->nullable();
            $table->json('workers')->nullable();
            $table->string('cctv')->nullable();
            $table->string('group_leader')->nullable();
            $table->string('section_head')->nullable();
            $table->string('she_leader')->nullable();
            $table->string('dept_head')->nullable();
            $table->string('pja_bc')->nullable();
            $table->timestamps();

            $table->index(['dop_safety_plan_id', 'section_name'], 'idx_dop_safety_plan_items_plan_section');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dop_safety_plan_items');
    }
};
