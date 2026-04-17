<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Exercise;
use App\Models\ExercisePlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExerciseSession>
 */
class ExerciseSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'exercise_plan_id' => ExercisePlan::factory(),
            'exercise_id' => Exercise::factory(),
            'started_at' => now(),
            'status' => 'started',
        ];
    }
}
