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
        Schema::create('game_play_storage_eloquent_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('gameInviteId')->unique()->nullable();
            $table->json('gameData')->nullable();
            $table->boolean('setup')->default(false);
            $table->boolean('finished')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_play_storage_eloquent_models');
    }
};
