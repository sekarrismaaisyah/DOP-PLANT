<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dopm_wa_notification_log', function (Blueprint $table) {
            $table->string('provider', 20)->nullable()->default('fonnte')->after('fonnte_response');
        });
    }

    public function down(): void
    {
        Schema::table('dopm_wa_notification_log', function (Blueprint $table) {
            $table->dropColumn('provider');
        });
    }
};
