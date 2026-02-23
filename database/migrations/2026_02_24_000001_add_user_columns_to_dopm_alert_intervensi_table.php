<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah informasi siapa yang melakukan intervensi ke tabel dopm_alert_intervensi.
     * Disimpan user_id + beberapa atribut dasar user untuk keperluan audit/log.
     */
    public function up(): void
    {
        Schema::table('dopm_alert_intervensi', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->after('alert_level')
                ->comment('ID user yang melakukan intervensi (Auth::id())');
            $table->string('user_name', 191)
                ->nullable()
                ->after('user_id')
                ->comment('Nama user saat intervensi');
            $table->string('user_username', 191)
                ->nullable()
                ->after('user_name')
                ->comment('Username / SID user saat intervensi');
            $table->string('user_email', 191)
                ->nullable()
                ->after('user_username')
                ->comment('Email user saat intervensi');

            $table->index('user_id', 'dopm_alert_intervensi_user_id_index');
        });
    }

    /**
     * Rollback perubahan kolom user di tabel dopm_alert_intervensi.
     */
    public function down(): void
    {
        Schema::table('dopm_alert_intervensi', function (Blueprint $table) {
            $table->dropIndex('dopm_alert_intervensi_user_id_index');
            $table->dropColumn(['user_id', 'user_name', 'user_username', 'user_email']);
        });
    }
};

