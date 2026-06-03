<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('mac_address')->unique();
            $table->string('hostname')->nullable();
            $table->string('vendor')->nullable();
            $table->string('device_type')->nullable();
            $table->string('system_name')->nullable();
            $table->text('system_description')->nullable();
            $table->boolean('snmp_enabled')->default(false);
            $table->string('snmp_version')->nullable();
            $table->string('snmp_community')->nullable();
            $table->enum('status', [
                'ONLINE',
                'OFFLINE'
            ])->default('ONLINE');
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamp('last_scan_at')->nullable();
            $table->string('last_event')->nullable();
            $table->text('last_event_message')->nullable();
            $table->timestamp('last_event_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
