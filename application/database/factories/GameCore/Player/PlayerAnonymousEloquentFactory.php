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
        $namePrefix = 'Anonymous';
        $nameMiddle = fake()->city();
        $nameSuffix = fake()->numberBetween(10000, 99999);

        return [
            'name' => "$namePrefix $nameMiddle $nameSuffix",
            'hash' => null,
        ];
    }
}
