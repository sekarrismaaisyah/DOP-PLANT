<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom PIC (Person In Charge) dan status ke tabel dopm_alert_intervensi.
     * PIC adalah user yang bertanggung jawab menangani dan menutup issue.
     */
    public function up(): void
    {
        Schema::table('dopm_alert_intervensi', function (Blueprint $table) {
            $table->unsignedBigInteger('pic_user_id')
                ->nullable()
                ->after('user_email')
                ->comment('ID user PIC yang bertanggung jawab');
            $table->string('pic_name', 191)
                ->nullable()
                ->after('pic_user_id')
                ->comment('Nama PIC saat di-assign');
            $table->string('pic_email', 191)
                ->nullable()
                ->after('pic_name')
                ->comment('Email PIC saat di-assign');

            $table->enum('status', ['open', 'in_progress', 'closed'])
                ->default('open')
                ->after('pic_email')
                ->comment('Status issue: open, in_progress, closed');

            $table->index('pic_user_id', 'dopm_alert_intervensi_pic_user_id_index');
            $table->index('status', 'dopm_alert_intervensi_status_index');
        });
    }

    /**
     * Rollback perubahan.
     */
    public function down(): void
    {
        Schema::table('dopm_alert_intervensi', function (Blueprint $table) {
            $table->dropIndex('dopm_alert_intervensi_pic_user_id_index');
            $table->dropIndex('dopm_alert_intervensi_status_index');
            $table->dropColumn(['pic_user_id', 'pic_name', 'pic_email', 'status']);
        });
    }
};
