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
        Schema::create('switch_ports', function (Blueprint $table) {

            $table->id();
        
            $table->foreignId('network_switch_id')
                ->constrained()
                ->cascadeOnDelete();
        
            $table->integer('port_index');
        
            $table->string('port_name');
        
            $table->json('connected_macs')->nullable();
        
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('switch_ports');
    }
};
