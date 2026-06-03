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
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('event_type');

            $table->string('severity')
                ->default('INFO');

            $table->string('old_ip')
                ->nullable();

            $table->string('new_ip')
                ->nullable();

            $table->string('old_mac')
                ->nullable();

            $table->string('new_mac')
                ->nullable();

            $table->text('message')
                ->nullable();

            $table->json('data')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
