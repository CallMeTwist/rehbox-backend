<?php

use App\Models\Client;
use App\Models\Exercise;
use App\Models\ExerciseCompletion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs exercise completion for authenticated client', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);
    $exercise = Exercise::factory()->create(['is_personalized' => false]);

    $this->actingAs($user)
        ->postJson("/api/client/exercises/{$exercise->id}/log-completion")
        ->assertCreated();

    expect(ExerciseCompletion::count())->toBe(1);
    $row = ExerciseCompletion::first();
    expect($row->exercise_id)->toBe($exercise->id);
    expect($row->client_id)->toBe($client->id);
});

it('returns 403 when authenticated user has no client profile', function () {
    $user = User::factory()->create(['role' => 'client']);
    $exercise = Exercise::factory()->create();

    $this->actingAs($user)
        ->postJson("/api/client/exercises/{$exercise->id}/log-completion")
        ->assertStatus(403);
});

it('requires authentication', function () {
    $exercise = Exercise::factory()->create();

    $this->postJson("/api/client/exercises/{$exercise->id}/log-completion")
        ->assertStatus(401);
});
