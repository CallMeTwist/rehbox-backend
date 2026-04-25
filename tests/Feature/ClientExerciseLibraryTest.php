<?php

use App\Models\Client;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('free client gets a flat list of generalized exercises only', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    Exercise::factory()->create(['is_personalized' => false, 'title' => 'Squat']);
    Exercise::factory()->create(['is_personalized' => true, 'title' => 'Custom Hip']);

    $response = $this->actingAs($user)->getJson('/api/client/exercises');

    $response->assertOk();
    $payload = $response->json('data');
    expect($payload)->toBeArray()->toHaveCount(1);
    expect($payload[0]['title'])->toBe('Squat');
});

it('paid client gets exercises grouped by category', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'standard']);

    Exercise::factory()->create(['is_personalized' => false, 'category' => 'lower_limb', 'title' => 'Squat']);
    Exercise::factory()->create(['is_personalized' => true, 'category' => 'back', 'title' => 'Hip Flex']);

    $response = $this->actingAs($user)->getJson('/api/client/exercises');

    $response->assertOk();
    $grouped = $response->json('data');
    expect($grouped)->toHaveKeys(['lower_limb', 'back']);
});
