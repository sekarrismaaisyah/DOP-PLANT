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
        Schema::create('hse_ai_validations', function (Blueprint $table) {
            $table->id();
            
            // Original data from ClickHouse (aaj_car_all_year_from_dav)
            $table->string('task_number')->nullable();
            $table->string('jenis_laporan')->nullable();
            $table->text('aktivitas_pekerjaan')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('detail_lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->datetime('tanggal_pelaporan')->nullable();
            $table->string('perusahaan_pelapor')->nullable();
            $table->string('pelapor')->nullable();
            $table->string('sid_pelapor')->nullable();
            $table->string('jabatan_fungsional_pelapor')->nullable();
            $table->string('departemen_pelapor')->nullable();
            $table->string('pic')->nullable();
            $table->string('sid_pic')->nullable();
            $table->string('jabatan_fungsional_pic')->nullable();
            $table->string('perusahaan_pic')->nullable();
            $table->string('departemen_pic')->nullable();
            $table->text('uri_foto')->nullable();
            $table->string('tools_pengawasan')->nullable();
            $table->text('catatan_tindakan')->nullable();
            $table->string('nik_pelapor')->nullable();
            $table->string('nama_pelapor')->nullable();
            $table->string('nama_perusahaan_pelapor_karyawan')->nullable();
            $table->string('jabatan_fungsional_karyawan_pelapor')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('site')->nullable();
            $table->text('keterangan_lokasi')->nullable();
            $table->string('jam')->nullable();
            $table->string('menit')->nullable();
            $table->string('nama_lokasi')->nullable();
            $table->string('nama_detail_lokasi')->nullable();
            
            // AI Validation Results
            $table->boolean('ai_match_found')->default(false);
            $table->string('ai_main_category')->nullable();
            $table->string('ai_sub_category')->nullable();
            $table->boolean('ai_tbc')->default(false);
            $table->boolean('ai_pspp')->default(false);
            $table->boolean('ai_gr')->default(false);
            $table->boolean('ai_incident')->default(false);
            $table->text('ai_justification')->nullable();
            $table->decimal('ai_confidence_score', 3, 2)->nullable();
            
            // Metadata
            $table->date('validation_date')->nullable(); // Tanggal validasi (hari ini)
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('task_number');
            $table->index('validation_date');
            $table->index('ai_tbc');
            $table->index('ai_gr');
            $table->index('tanggal_pelaporan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hse_ai_validations');
    }
};

