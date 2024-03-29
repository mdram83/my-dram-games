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
        Schema::create('game_invite_eloquent_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->string('gameBox', 255)->nullable();
            $table->json('options')->nullable();
            $table->string('hostable_id', 255)->nullable();
            $table->string('hostable_type', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_invite_eloquent_models');
    }
};
