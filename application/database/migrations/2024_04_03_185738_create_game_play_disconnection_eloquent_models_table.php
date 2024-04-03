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
        Schema::create('gameplay_disconnects_model', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('game_play_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->references('id')
                ->on('game_play_storage_eloquent_models');

            $table->string('playerable_id', 255);
            $table->string('playerable_type', 255);

            $table->timestamp('disconnected_at');

            $table->unique(['game_play_id', 'playerable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gameplay_disconnects_model');
    }
};
