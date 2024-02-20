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
        Schema::create('game_invite_players', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('game_invite_eloquent_model_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->references('id')
                ->on('game_invite_eloquent_models');

            $table->string('game_invite_player_id', 255)->nullable();
            $table->string('game_invite_player_type', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_player');
    }
};
