<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dopm_weekly_imports')) {
            return;
        }

        Schema::create('dopm_weekly_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('row_no')->nullable();
            $table->string('kode_ikk', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('site', 100)->nullable();
            $table->string('jenis_ijin_kerja_khusus', 255)->nullable();
            $table->text('nama_pekerjaan')->nullable();
            $table->string('perusahaan', 255)->nullable();
            $table->string('status_wp', 100)->nullable();
            $table->string('pic_approver', 255)->nullable();
            $table->string('nama_layer_1', 255)->nullable();
            $table->string('sid_layer_1', 100)->nullable();
            $table->string('nama_layer_2', 255)->nullable();
            $table->string('sid_layer_2', 100)->nullable();
            $table->string('nama_layer_3', 255)->nullable();
            $table->string('sid_layer_3', 100)->nullable();
            $table->string('nama_layer_4', 255)->nullable();
            $table->string('sid_layer_4', 100)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('location', 255)->nullable();
            $table->text('location_detail')->nullable();
            $table->boolean('ada_ipk')->default(false);
            $table->string('kode_ipk', 255)->nullable();
            $table->text('detail_ipk')->nullable();
            $table->boolean('ada_okk')->default(false);
            $table->text('kode_okk')->nullable();
            $table->text('detail_okk')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'site'], 'dopm_weekly_imports_tanggal_site_idx');
            $table->index('kode_ikk', 'dopm_weekly_imports_kode_ikk_idx');
            $table->index(['start_date', 'end_date'], 'dopm_weekly_imports_start_end_idx');
            $table->unique(['kode_ikk', 'start_date'], 'dopm_weekly_imports_kode_start_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dopm_weekly_imports');
    }
};
