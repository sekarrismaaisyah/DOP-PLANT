<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minute_issues', function (Blueprint $table): void {
            $table->timestamp('closed_at')->nullable()->after('status');
            $table->string('closed_by_sid', 50)->nullable()->after('closed_at');
            $table->string('closed_by_name')->nullable()->after('closed_by_sid');
        });
    }

    public function down(): void
    {
        Schema::table('minute_issues', function (Blueprint $table): void {
            $table->dropColumn(['closed_at', 'closed_by_sid', 'closed_by_name']);
        });
    }
};
