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
        Schema::create('network_switches', function (Blueprint $table) {

            $table->id();
        
            $table->string('hostname')->nullable();
        
            $table->string('ip_address')->unique();
        
            $table->string('mac_address')->unique();
        
            $table->string('vendor')->nullable();
        
            $table->string('model')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_switches');
    }
};
