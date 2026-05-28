<?php

use App\Models\Client;
use App\Models\CoinTransaction;
use App\Models\Exercise;
use App\Models\ExercisePlan;
use App\Models\ExerciseSession;
use App\Models\Physiotherapist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('awards coins via awardCoins() and creates a CoinTransaction when completing a session', function () {
    // Create PT (needed for ExercisePlan)
    $ptUser = User::factory()->create(['role' => 'pt']);
    $pt = Physiotherapist::factory()->create(['user_id' => $ptUser->id]);

    // Create client user + client record
    $clientUser = User::factory()->create(['role' => 'client']);
    $client = Client::factory()->create([
        'user_id' => $clientUser->id,
        'subscription_plan' => 'standard',
        'subscription_status' => 'active',
        'subscription_expires_at' => now()->addMonth(),
        'coin_balance' => 0,
    ]);

    // Create exercise
    $exercise = Exercise::factory()->create(['title' => 'Knee Extension']);

    // Create exercise plan
    $plan = ExercisePlan::factory()->create([
        'physiotherapist_id' => $pt->id,
        'client_id' => $client->id,
    ]);

    // Create a started session
    $session = ExerciseSession::factory()->create([
        'client_id' => $client->id,
        'exercise_plan_id' => $plan->id,
        'exercise_id' => $exercise->id,
        'status' => 'started',
    ]);

    $response = $this->actingAs($clientUser, 'sanctum')
        ->putJson("/api/client/sessions/{$session->id}/complete", [
            'form_score' => 85,
        ]);

    $response->assertSuccessful();

    // A CoinTransaction record must exist for this client
    expect(CoinTransaction::where('client_id', $client->id)->exists())->toBeTrue();

    // Transaction details: 85% form score → 3 coins, type 'earned', description includes exercise title
    $transaction = CoinTransaction::where('client_id', $client->id)->first();
    expect($transaction->amount)->toBe(3)
        ->and($transaction->type)->toBe('earned')
        ->and($transaction->description)->toContain('Knee Extension');

    // Client coin_balance must be 3
    expect($client->fresh()->coin_balance)->toBe(3);

    // PT should also receive a notification
    expect(\App\Models\AppNotification::where('user_id', $ptUser->id)->exists())->toBeTrue();
});
