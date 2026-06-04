<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peer_pressure_data_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 255);
            $table->string('original_filename', 255)->nullable();
            $table->string('status', 16);
            $table->text('message')->nullable();
            $table->json('validation_errors')->nullable();
            $table->unsignedInteger('imported_kejadian')->default(0);
            $table->unsignedInteger('imported_peserta')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_pressure_data_import_logs');
    }
};
