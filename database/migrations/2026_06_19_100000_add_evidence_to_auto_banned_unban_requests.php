<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auto_banned_unban_requests', function (Blueprint $table): void {
            $table->string('evidence_file_path', 512)->nullable()->after('alasan_pengajuan');
            $table->string('evidence_original_name', 255)->nullable()->after('evidence_file_path');
            $table->string('evidence_mime', 128)->nullable()->after('evidence_original_name');
            $table->dateTime('evidence_uploaded_at')->nullable()->after('evidence_mime');

            $table->index(['week', 'iso_year', 'sid'], 'idx_auto_banned_unban_period_sid');
        });
    }

    public function down(): void
    {
        Schema::table('auto_banned_unban_requests', function (Blueprint $table): void {
            $table->dropIndex('idx_auto_banned_unban_period_sid');
            $table->dropColumn([
                'evidence_file_path',
                'evidence_original_name',
                'evidence_mime',
                'evidence_uploaded_at',
            ]);
        });
    }
};
