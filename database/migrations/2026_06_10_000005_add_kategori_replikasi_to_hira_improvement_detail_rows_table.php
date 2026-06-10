<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hira_improvement_detail_rows', function (Blueprint $table) {
            $table->string('perusahaan', 255)->default('Bukit Makmur')->after('site');
            $table->string('kategori', 64)->default('Non Replikasi (New)')->after('perusahaan');
            $table->text('replikasi_list')->nullable()->after('kategori');
            $table->string('replikasi_inisiator_site', 128)->nullable()->after('replikasi_list');

            $table->index('kategori', 'idx_hira_detail_kategori');
            $table->index(['site', 'perusahaan'], 'idx_hira_detail_site_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('hira_improvement_detail_rows', function (Blueprint $table) {
            $table->dropIndex('idx_hira_detail_kategori');
            $table->dropIndex('idx_hira_detail_site_perusahaan');
            $table->dropColumn([
                'perusahaan',
                'kategori',
                'replikasi_list',
                'replikasi_inisiator_site',
            ]);
        });
    }
};
