<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dopm_wa_notification_log', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('kode_ikk', 100)->index();
            $table->unsignedTinyInteger('alert_level')->default(1);
            $table->unsignedTinyInteger('layer')->nullable()->comment('Layer 1/2/3/4 yang dikirim');
            $table->string('phone_number', 30)->nullable();
            $table->string('recipient_name', 150)->nullable();
            $table->string('recipient_sid', 50)->nullable();
            $table->text('message')->nullable();
            $table->string('fonnte_status', 50)->nullable()->comment('Status response dari Fonnte: success/failed/pending');
            $table->string('fonnte_id', 100)->nullable()->comment('ID dari Fonnte untuk tracking');
            $table->text('fonnte_response')->nullable()->comment('Full response JSON dari Fonnte');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'kode_ikk', 'alert_level']);
            $table->index(['tanggal', 'kode_ikk', 'alert_level', 'layer', 'phone_number'], 'wa_log_unique_check');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dopm_wa_notification_log');
    }
};
