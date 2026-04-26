<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    Route::middleware(['auth:sanctum', 'require.paid.tier'])
        ->get('/_test/paid', fn () => response()->json(['ok' => true]));
});

it('allows paid clients', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'standard']);

    $this->actingAs($user)->getJson('/_test/paid')->assertOk();
});

it('blocks free clients with 403', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'free']);

    $this->actingAs($user)->getJson('/_test/paid')
        ->assertForbidden()
        ->assertJson(['message' => 'This feature requires a paid subscription.']);
});
