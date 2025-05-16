<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Tax Bracket ' . $this->faker->numberBetween(1, 5),
            'description' => $this->faker->sentence(),
            'rate' => $this->faker->randomElement([0.05, 0.15, 0.25, 0.3]),
            'threshold' => $this->faker->randomElement(['0-60000000', '60000001-250000000', '250000001-500000000']),
        ];
    }
}
