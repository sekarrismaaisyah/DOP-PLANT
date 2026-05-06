<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('code')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('meeting_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('code')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('company_site', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_required')->default(true)->index();
            $table->timestamps();

            $table->unique(['company_id', 'site_id']);
        });

        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_sid')->unique();
            $table->string('nama')->index();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('jabatan_struktural')->nullable();
            $table->string('jabatan_fungsional')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->index(['kode_sid', 'is_active']);
        });

        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_code')->unique();
            $table->string('qr_token', 64)->unique();
            $table->foreignId('meeting_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('site_id')->constrained()->restrictOnDelete();
            $table->date('meeting_date')->index();
            $table->string('week', 16)->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('draft')->index();
            $table->timestamp('closed_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['site_id', 'week', 'status']);
            $table->index(['meeting_type_id', 'meeting_date']);
        });

        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('kode_sid')->index();
            $table->string('nama_snapshot');
            $table->string('perusahaan_snapshot');
            $table->string('jabatan_struktural_snapshot')->nullable();
            $table->string('jabatan_fungsional_snapshot')->nullable();
            $table->enum('input_method', ['qr', 'manual'])->default('qr')->index();
            $table->dateTime('attended_at')->index();
            $table->timestamps();

            $table->unique(['event_id', 'employee_id']);
            $table->index(['event_id', 'attended_at']);
        });

        Schema::create('event_minutes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained('events')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('notulis')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('minute_issues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_minute_id')->constrained('event_minutes')->cascadeOnDelete();
            $table->enum('section', ['enviro', 'safety', 'general'])->index();
            $table->unsignedInteger('nomor')->index();
            $table->text('catatan_meeting');
            $table->string('issued_by')->nullable()->index();
            $table->string('pic')->nullable()->index();
            $table->date('due_date')->nullable()->index();
            $table->enum('status', ['Open', 'Progress', 'Closed', 'Overdue'])->default('Open')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minute_issues');
        Schema::dropIfExists('event_minutes');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('events');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('company_site');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('meeting_types');
        Schema::dropIfExists('sites');
    }
};
