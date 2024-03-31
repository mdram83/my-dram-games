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
        Schema::create('game_record_eloquent_models', function (Blueprint $table) {
            $table->id();

            $table->json('score');
            $table->boolean('winnerFlag')->default(false);

            $table->foreignUuid('game_invite_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->references('id')
                ->on('game_invite_eloquent_models');

            $table->string('playerable_id', 255);
            $table->string('playerable_type', 255);

            $table->timestamps();

            $table->unique(['game_invite_id', 'playerable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_record_eloquent_models');
    }
};
