<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_hsct_campaign_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('auto_banned_hsct_campaigns')->cascadeOnDelete();
            $table->unsignedBigInteger('snapshot_id')->nullable();
            $table->string('sid', 64);
            $table->string('karyawan', 255);
            $table->string('perusahaan', 255)->nullable();
            $table->string('site_dedicated', 255)->nullable();
            $table->string('banned_reason', 255)->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'sid'], 'uq_auto_banned_hsct_item_campaign_sid');
            $table->index('is_confirmed', 'idx_auto_banned_hsct_item_confirmed');
            $table->index('snapshot_id', 'idx_auto_banned_hsct_item_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_hsct_campaign_items');
    }
};
