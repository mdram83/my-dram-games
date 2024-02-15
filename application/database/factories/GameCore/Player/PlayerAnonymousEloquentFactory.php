<?php

namespace Database\Factories\GameCore\Player;

use App\Models\GameCore\Player\PlayerAnonymousEloquent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerAnonymousEloquent>
 */
class PlayerAnonymousEloquentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Anonymous ' . str_replace(' ', '', fake()->city() . fake()->numberBetween(1000, 9999)),
            'hash' => null,
        ];
    }
}
