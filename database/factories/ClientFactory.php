<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'client']),
            'phone' => fake()->phoneNumber(),
            'subscription_status' => 'active',
            'coin_balance' => 0,
            'language_preference' => 'en',
        ];
    }
}
