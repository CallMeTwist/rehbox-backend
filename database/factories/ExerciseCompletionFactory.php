<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExerciseCompletion>
 */
class ExerciseCompletionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'exercise_id' => Exercise::factory(),
            'completed_at' => now(),
        ];
    }
}
