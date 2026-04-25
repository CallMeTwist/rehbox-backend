<?php

use App\Models\Client;
use App\Models\ClientAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function validAssessmentPayload(): array
{
    return [
        'medical_conditions' => 'None',
        'height_cm' => 175, 'weight_kg' => 72,
        'past_injuries' => 'None', 'allergies' => 'None',
        'smokes' => false, 'alcohol_consumption' => 'rarely',
        'diet_preferences' => 'None', 'stress_level' => 5,
        'primary_goals' => ['🚀 Body Fat/Weight Loss'],
        'secondary_goals' => 'None', 'time_frame' => '90d',
        'exercise_habit' => 'newbie',
        'weekly_schedule' => '3 days - 30 mins',
        'comfort_level' => ['🏃 Easy to Perform'],
        'best_time' => 'morning',
        'feedback_frequency' => 'weekly',
        'feedback_type' => ['📈 Progress updates & Future improvements'],
        'feedback_channel' => 'email',
    ];
}

it('submits assessment and marks completed', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $this->actingAs($user)
        ->postJson('/api/client/assessment', validAssessmentPayload())
        ->assertCreated();

    expect($client->fresh()->assessment_completed_at)->not->toBeNull();
    expect(ClientAssessment::where('client_id', $client->id)->count())->toBe(1);
});

it('rejects invalid payload (missing required fields)', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $this->actingAs($user)
        ->postJson('/api/client/assessment', ['medical_conditions' => 'a'])
        ->assertStatus(422);
});

it('updates existing assessment instead of creating duplicate', function () {
    $user = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $this->actingAs($user)->postJson('/api/client/assessment', validAssessmentPayload())->assertCreated();

    $second = validAssessmentPayload();
    $second['weight_kg'] = 80;
    $this->actingAs($user)->postJson('/api/client/assessment', $second)->assertCreated();

    expect(ClientAssessment::where('client_id', $client->id)->count())->toBe(1);
    expect(ClientAssessment::where('client_id', $client->id)->first()->weight_kg)->toBe(80);
});

it('show returns 404 when no assessment yet', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $this->actingAs($user)->getJson('/api/client/assessment')->assertStatus(404);
});

it('show returns assessment after submit', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $this->actingAs($user)->postJson('/api/client/assessment', validAssessmentPayload())->assertCreated();
    $this->actingAs($user)->getJson('/api/client/assessment')->assertOk()
        ->assertJsonPath('data.height_cm', 175);
});
