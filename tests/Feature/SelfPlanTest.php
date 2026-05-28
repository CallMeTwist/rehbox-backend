<?php

use App\Models\Client;
use App\Models\Exercise;
use App\Models\ExercisePlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a self-built plan for a subscribed client', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);
    $exercises = Exercise::factory()->count(3)->create(['is_personalized' => false]);

    $this->actingAs($user)->postJson('/api/client/plans/self', [
        'title' => 'Morning Routine',
        'exercise_ids' => $exercises->pluck('id')->all(),
        'scheduled_days' => ['Mon', 'Wed', 'Fri'],
    ])->assertCreated();

    expect(ExercisePlan::selfBuilt()->count())->toBe(1);
    expect(ExercisePlan::first()->created_by_client_id)->toBe($client->id);
    expect(ExercisePlan::first()->is_self_built)->toBeTrue();
});

it('rejects more than 3 exercises', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);
    $exercises = Exercise::factory()->count(4)->create(['is_personalized' => false]);

    $this->actingAs($user)->postJson('/api/client/plans/self', [
        'title' => 'Too Many',
        'exercise_ids' => $exercises->pluck('id')->all(),
        'scheduled_days' => ['Mon'],
    ])->assertStatus(422);
});

it('rejects personalized exercises', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);
    $ex = Exercise::factory()->create(['is_personalized' => true]);

    $this->actingAs($user)->postJson('/api/client/plans/self', [
        'title' => 'Bad',
        'exercise_ids' => [$ex->id],
        'scheduled_days' => ['Mon'],
    ])->assertStatus(422);
});

it('updates an existing self-built plan owned by the client', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);
    $exercises = Exercise::factory()->count(2)->create(['is_personalized' => false]);

    $plan = ExercisePlan::create([
        'client_id' => $client->id,
        'created_by_client_id' => $client->id,
        'is_self_built' => true,
        'title' => 'Old',
        'status' => 'active',
        'start_date' => now(),
    ]);

    $this->actingAs($user)->putJson("/api/client/plans/self/{$plan->id}", [
        'title' => 'New Name',
        'exercise_ids' => $exercises->pluck('id')->all(),
        'scheduled_days' => ['Tue'],
    ])->assertOk();

    expect($plan->fresh()->title)->toBe('New Name');
});

it('forbids updating a self-built plan owned by another client', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);
    $otherClient = Client::factory()->create([
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);
    $exercise = Exercise::factory()->create(['is_personalized' => false]);

    $plan = ExercisePlan::create([
        'client_id' => $otherClient->id,
        'created_by_client_id' => $otherClient->id,
        'is_self_built' => true,
        'title' => 'Theirs',
        'status' => 'active',
        'start_date' => now(),
    ]);

    $this->actingAs($user)->putJson("/api/client/plans/self/{$plan->id}", [
        'title' => 'Hijack',
        'exercise_ids' => [$exercise->id],
        'scheduled_days' => ['Mon'],
    ])->assertStatus(403);
});

it('deletes a self-built plan', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $user->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
    ]);

    $plan = ExercisePlan::create([
        'client_id' => $client->id,
        'created_by_client_id' => $client->id,
        'is_self_built' => true,
        'title' => 'Will be gone',
        'status' => 'active',
        'start_date' => now(),
    ]);

    $this->actingAs($user)->deleteJson("/api/client/plans/self/{$plan->id}")->assertOk();

    expect(ExercisePlan::find($plan->id))->toBeNull();
});
