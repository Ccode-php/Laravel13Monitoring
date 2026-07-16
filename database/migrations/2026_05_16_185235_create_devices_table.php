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
        
            $table->string('name')->nullable();
        
            $table->string('ip_address');
        
            $table->string('mac_address')->unique();
        
            $table->enum('status', [
                'ONLINE',
                'OFFLINE'
            ])->default('ONLINE');
        
            $table->timestamp('last_seen_at')->nullable();
        
            $table->foreignId('switch_port_id')
                ->nullable()
                ->constrained('switch_ports')
                ->nullOnDelete();
        
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
