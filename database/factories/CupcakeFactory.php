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
        $flavors = ['sucré', 'salé', 'mix'];

        return [
            'name' => fake()->colorName(),
            'quantity' => random_int(0, 100),
            'flavor' => $flavors[random_int(0,2)],
            'is_available' => fake()->boolean(70),
            'is_advertised' => fake()->boolean(20),
            'price_in_cents' => random_int(250, 500)
      ];
    }

    public function is_available() {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
        ]);
    }

    public function is_unavailable() {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
