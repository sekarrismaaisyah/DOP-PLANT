<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembatasan_lv_inputasi', function (Blueprint $table) {
            $table->dateTime('checkout_at')->nullable()->after('checkin_at');
            $table->unsignedBigInteger('checkout_by_id')->nullable()->after('checkout_at');
            $table->string('checkout_by_name', 255)->nullable()->after('checkout_by_id');

            $table->index(['control_room', 'checkout_at']);
        });
    }

    public function down(): void
    {
        Schema::table('pembatasan_lv_inputasi', function (Blueprint $table) {
            $table->dropIndex(['control_room', 'checkout_at']);
            $table->dropColumn(['checkout_at', 'checkout_by_id', 'checkout_by_name']);
        });
    }
};
