<?php

use App\Models\Client;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('returns only free-tier exercises filtered by client condition for free clients', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'free',
        'primary_condition' => 'lower_back_pain',
    ]);

    Exercise::factory()->create(['access_tier' => 'free', 'area' => 'back', 'is_personalized' => false, 'is_active' => true]);
    Exercise::factory()->create(['access_tier' => 'free', 'area' => 'lower_limbs', 'is_personalized' => false, 'is_active' => true]);
    Exercise::factory()->create(['access_tier' => 'paid', 'area' => 'back', 'is_personalized' => false, 'is_active' => true]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/client/exercises');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1)
        ->and($data[0]['area'])->toBe('back')
        ->and($data[0]['access_tier'])->toBe('free');
});

it('returns all active free + paid exercises for paid clients', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);

    Exercise::factory()->create(['access_tier' => 'free', 'is_personalized' => false, 'is_active' => true]);
    Exercise::factory()->create(['access_tier' => 'paid', 'is_personalized' => false, 'is_active' => true]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/client/exercises');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});

it('returns streak-only payload for free clients on progress endpoint', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'free',
    ]);

    $exercise = \App\Models\Exercise::factory()->create(['access_tier' => 'free']);

    foreach ([0, 1, 2] as $daysAgo) {
        \App\Models\ExerciseCompletion::factory()->create([
            'client_id' => $client->id,
            'exercise_id' => $exercise->id,
            'completed_at' => now()->subDays($daysAgo),
        ]);
    }

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/client/progress');

    $response->assertOk();
    $response->assertJsonStructure(['current_streak', 'longest_streak', 'last_7_days']);
    $response->assertJsonMissing(['sessions', 'pain_levels', 'compliance']);
    expect($response->json('current_streak'))->toBe(3)
        ->and($response->json('last_7_days'))->toBe([false, false, false, false, true, true, true]);
});

it('blocks a second reminder for free clients', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'free',
    ]);

    \App\Models\Reminder::factory()->create(['client_id' => $client->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/client/reminders', [
        'type' => 'exercise',
        'times' => ['09:00'],
        'days' => ['monday', 'wednesday', 'friday'],
    ]);

    $response->assertForbidden();
});

it('allows paid clients to add multiple reminders', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);

    \App\Models\Reminder::factory()->create(['client_id' => $client->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/client/reminders', [
        'type' => 'exercise',
        'times' => ['09:00'],
        'days' => ['monday', 'wednesday', 'friday'],
    ]);

    $response->assertCreated();
});

it('awards 50 coins to a free client on log-completion', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'free',
        'coin_balance' => 0,
    ]);
    $exercise = Exercise::factory()->create(['access_tier' => 'free']);

    Sanctum::actingAs($user);

    $this->postJson("/api/client/exercises/{$exercise->id}/log-completion", [
    ])->assertSuccessful();

    expect($client->fresh()->coin_balance)->toBe(50);
});

it('does not award log-completion coins to a paid client', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
        'coin_balance' => 0,
    ]);
    $exercise = Exercise::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson("/api/client/exercises/{$exercise->id}/log-completion", [
    ])->assertSuccessful();

    expect($client->fresh()->coin_balance)->toBe(0);
});

it('returns locked plan payload for free clients', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'free',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/client/plan');

    $response->assertOk();
    expect($response->json('plan'))->toBeNull()
        ->and($response->json('locked'))->toBeTrue()
        ->and($response->json('reason'))->toBe('free_tier');
});

it('blocks free clients from chat, connect-pt, self-plan, and shop routes', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'free',
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/client/chat')->assertForbidden();
    $this->postJson('/api/client/chat', ['body' => 'hi'])->assertForbidden();
    $this->postJson('/api/client/connect-pt', ['pt_code' => 'XYZ'])->assertForbidden();
    $this->getJson('/api/client/shop')->assertForbidden();
    $this->postJson('/api/client/plans/self', ['title' => 'My plan'])->assertForbidden();
});

it('allows paid clients to reach chat, shop, connect-pt, self-plan', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/client/chat')->assertOk();
    $this->getJson('/api/client/shop')->assertOk();
});
