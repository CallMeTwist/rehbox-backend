<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Physiotherapist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExercisePlan>
 */
class ExercisePlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'physiotherapist_id' => Physiotherapist::factory(),
            'client_id' => Client::factory(),
            'title' => fake()->words(4, true),
            'status' => 'active',
            'frequency' => 'daily',
        ];
    }
}
