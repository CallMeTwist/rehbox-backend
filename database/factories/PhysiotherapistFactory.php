<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Physiotherapist>
 */
class PhysiotherapistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'pt']),
            'license_number' => strtoupper(Str::random(8)),
            'hospital_or_clinic' => fake()->company(),
            'specialty' => 'Orthopaedics',
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'country' => 'Nigeria',
            'vetting_status' => 'approved',
            'activation_code' => strtoupper(Str::random(8)),
        ];
    }
}
