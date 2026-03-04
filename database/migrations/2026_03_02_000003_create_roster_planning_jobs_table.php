<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roster_planning_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id', 100)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('dop_created')->default(0);
            $table->integer('dop_updated')->default(0);
            $table->integer('ikk_created')->default(0);
            $table->integer('ikk_updated')->default(0);
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roster_planning_jobs');
    }
};
