<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'type' => $this->faker->randomElement(['exercise', 'posture', 'hydration', 'diet']),
            'times' => ['08:00', '14:00'],
            'days' => ['monday', 'wednesday', 'friday'],
            'is_active' => true,
        ];
    }
}
