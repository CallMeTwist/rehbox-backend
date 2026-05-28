<?php

use App\Models\Client;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('free client gets a flat list of free-tier generalized exercises only', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    Exercise::factory()->create(['is_personalized' => false, 'access_tier' => 'free', 'title' => 'Squat']);
    Exercise::factory()->create(['is_personalized' => false, 'access_tier' => 'paid', 'title' => 'Squat Paid']);
    Exercise::factory()->create(['is_personalized' => true, 'access_tier' => 'free', 'title' => 'Custom Hip']);

    $response = $this->actingAs($user)->getJson('/api/client/exercises');

    $response->assertOk();
    $payload = $response->json('data');
    expect($payload)->toBeArray()->toHaveCount(1);
    expect($payload[0]['title'])->toBe('Squat');
    foreach ($payload as $row) {
        expect($row['is_personalized'])->toBeFalse();
        expect($row['access_tier'])->toBe('free');
    }
});

it('paid client gets a flat list of generalized exercises', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);

    Exercise::factory()->create(['is_personalized' => false, 'category' => 'strengthening', 'title' => 'Squat']);
    Exercise::factory()->create(['is_personalized' => true, 'category' => 'stretching', 'title' => 'Hip Flex']);

    $response = $this->actingAs($user)->getJson('/api/client/exercises');

    $response->assertOk();
    $payload = $response->json('data');
    expect($payload)->toBeArray()->toHaveCount(1);
    expect($payload[0]['title'])->toBe('Squat');
});

it('excludes paid exercises entirely for free clients', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->for($user)->create(['subscription_plan' => 'free']);

    Exercise::factory()->create(['area' => 'back', 'access_tier' => 'paid', 'video_source' => 'upload', 'video_path' => 'x.mp4']);
    Exercise::factory()->free()->create();

    $response = $this->actingAs($user)->getJson('/api/client/exercises');
    $response->assertOk();

    $data = $response->json('data');
    $paidCount = collect($data)->where('access_tier', 'paid')->count();
    $freeCount = collect($data)->where('access_tier', 'free')->count();

    expect($paidCount)->toBe(0);
    expect($freeCount)->toBe(1);
});

it('filters by area and category', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->for($user)->create([
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);

    Exercise::factory()->create(['area' => 'back', 'category' => 'strengthening']);
    Exercise::factory()->create(['area' => 'chest', 'category' => 'strengthening']);

    $response = $this->actingAs($user)->getJson('/api/client/exercises?area=back');
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.area'))->toBe('back');
});

it('returns 403 when authenticated user has no client profile', function () {
    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user)->getJson('/api/client/exercises')->assertStatus(403);
});
