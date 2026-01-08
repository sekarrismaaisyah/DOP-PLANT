<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dms_calibrations', function (Blueprint $table) {
            $table->id();
            $table->string('driver_id', 50);
            $table->string('trip_id', 50)->nullable();
            $table->timestamp('calibration_start_time');
            $table->timestamp('calibration_end_time');
            $table->decimal('t_close', 10, 6);
            $table->decimal('ear_mean', 10, 6);
            $table->decimal('ear_sd', 10, 6);
            $table->unsignedInteger('data_points_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['driver_id', 'calibration_start_time']);
            $table->index('trip_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dms_calibrations');
    }
};

