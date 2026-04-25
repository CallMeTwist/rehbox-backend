<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns subscription_plan in auth/me-style response', function () {
    $user = User::factory()->create(['role' => 'client']);
    Client::factory()->create(['user_id' => $user->id, 'subscription_plan' => 'standard']);
    $resp = $this->actingAs($user)->getJson('/api/me');
    $resp->assertOk()->assertJsonPath('subscription_plan', 'standard');
});
