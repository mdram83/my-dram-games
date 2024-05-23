<?php

namespace Database\Factories;

use App\Models\PlayerAnonymousEloquent;
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
        $namePrefix = '';
        $nameMiddle = fake()->city();
        $nameSuffix = fake()->numberBetween(1000, 9999);

        return [
            'name' => "$namePrefix$nameMiddle$nameSuffix",
            'hash' => null,
        ];
    }
}
