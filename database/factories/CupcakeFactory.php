<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Type\Integer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cupcake>
 */
class CupcakeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->colorName(),
            'quantity' => random_int(0, 500),
            'is_available' => fake()->boolean(70),
            'is_advertised' => fake()->boolean(20),
            'price_in_cents' => random_int(250, 500)
      ];
    }
}
