<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'category' => fake()->randomElement(['head_neck', 'upper_limb', 'back', 'lower_limb']),
            'difficulty' => 'beginner',
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
