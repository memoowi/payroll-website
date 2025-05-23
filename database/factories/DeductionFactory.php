<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deduction>
 */
class DeductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' Deduction',
            'description' => $this->faker->sentence(10),
            'amount' => $this->faker->randomFloat(2, 10000, 500000),
            'applies_to_all' => $this->faker->boolean(),
        ];
    }
}
