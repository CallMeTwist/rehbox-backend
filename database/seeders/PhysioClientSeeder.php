<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Physiotherapist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PhysioClientSeeder extends Seeder
{
    public function run(): void
    {
        $physios = [
            [
                'user' => ['name' => 'Dr Amaka Obi', 'email' => 'dr.amaka@rehbox.dev'],
                'pt' => ['specialty' => 'Musculoskeletal', 'hospital_or_clinic' => 'Lagos General Hospital', 'city' => 'Lagos', 'country' => 'Nigeria', 'activation_code' => 'AMAKA001'],
            ],
            [
                'user' => ['name' => 'Dr Chidi Nwosu', 'email' => 'dr.chidi@rehbox.dev'],
                'pt' => ['specialty' => 'Sports & Orthopaedic', 'hospital_or_clinic' => 'Abuja Sports Medicine Centre', 'city' => 'Abuja', 'country' => 'Nigeria', 'activation_code' => 'CHIDI001'],
            ],
            [
                'user' => ['name' => 'Dr Emeka Eze', 'email' => 'dr.emeka@rehbox.dev'],
                'pt' => ['specialty' => 'Neurological', 'hospital_or_clinic' => 'Port Harcourt Neuro Clinic', 'city' => 'Port Harcourt', 'country' => 'Nigeria', 'activation_code' => 'EMEKA001'],
            ],
        ];

        $ptIds = [];

        foreach ($physios as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['user']['email']],
                array_merge($data['user'], [
                    'password' => Hash::make('password'),
                    'role' => 'pt',
                ])
            );

            $pt = Physiotherapist::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($data['pt'], [
                    'vetting_status' => 'approved',
                    'vetted_at' => now(),
                    'license_number' => strtoupper($data['pt']['activation_code']).'-LIC',
                    'phone' => '',
                ])
            );

            $ptIds[] = $pt->id;
        }

        $clients = [
            ['name' => 'Fatima Bello', 'email' => 'client1@rehbox.dev', 'plan' => 'standard', 'condition' => 'Lower back pain', 'pt' => 0],
            ['name' => 'Kunle Adeyemi', 'email' => 'client2@rehbox.dev', 'plan' => 'standard', 'condition' => 'Knee osteoarthritis', 'pt' => 0],
            ['name' => 'Ngozi Okafor', 'email' => 'client3@rehbox.dev', 'plan' => 'standard', 'condition' => 'Rotator cuff injury', 'pt' => 1],
            ['name' => 'Taiwo Lawal', 'email' => 'client4@rehbox.dev', 'plan' => 'standard', 'condition' => 'Post-ACL reconstruction', 'pt' => 1],
            ['name' => 'Seun Afolabi', 'email' => 'client5@rehbox.dev', 'plan' => 'free', 'condition' => 'Neck stiffness', 'pt' => 2],
            ['name' => 'Yetunde Bakare', 'email' => 'client6@rehbox.dev', 'plan' => 'free', 'condition' => 'Shoulder impingement', 'pt' => 2],
        ];

        foreach ($clients as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'client',
                ]
            );

            Client::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'physiotherapist_id' => $ptIds[$data['pt']],
                    'primary_condition' => $data['condition'],
                    'subscription_plan' => $data['plan'],
                    'subscription_status' => $data['plan'] === 'standard' ? 'active' : 'inactive',
                    'subscription_expires_at' => $data['plan'] === 'standard' ? Carbon::now()->addYear() : null,
                    'language_preference' => 'en',
                    'coin_balance' => 0,
                    'gender' => 'other',
                ]
            );
        }

        $this->command->info('Seeded 3 PTs and 6 clients.');
    }
}
