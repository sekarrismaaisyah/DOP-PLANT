<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('auto_banned_unban_requests')) {
            return;
        }

        Schema::table('auto_banned_unban_requests', function (Blueprint $table): void {
            if (! Schema::hasColumn('auto_banned_unban_requests', 'no_hp')) {
                $table->string('no_hp', 32)->nullable()->after('submitted_by_name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('auto_banned_unban_requests')) {
            return;
        }

        Schema::table('auto_banned_unban_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('auto_banned_unban_requests', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
        });
    }
};
