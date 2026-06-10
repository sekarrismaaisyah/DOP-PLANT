<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembatasan_orang_inputasi', function (Blueprint $table) {
            $table->string('dept', 255)->nullable()->after('site');

            $table->index('dept', 'idx_pembatasan_orang_inputasi_dept');
        });
    }

    public function down(): void
    {
        Schema::table('pembatasan_orang_inputasi', function (Blueprint $table) {
            $table->dropIndex('idx_pembatasan_orang_inputasi_dept');
            $table->dropColumn('dept');
        });
    }
};
