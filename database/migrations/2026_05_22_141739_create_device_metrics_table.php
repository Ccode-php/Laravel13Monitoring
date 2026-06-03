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
        Schema::create('device_metrics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->float('cpu')
                ->nullable();

            $table->float('memory')
                ->nullable();

            $table->float('temperature')
                ->nullable();

            $table->bigInteger('network_in')
                ->nullable();

            $table->bigInteger('network_out')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_metrics');
    }
};
