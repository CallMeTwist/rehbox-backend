<?php

use App\Models\Exercise;
use App\Models\Physiotherapist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters PT exercise list by access_tier', function () {
    $pt = User::factory()->create(['role' => 'pt']);
    Physiotherapist::factory()->for($pt)->create();

    Exercise::factory()->create(['access_tier' => 'paid', 'area' => 'back']);
    Exercise::factory()->free()->create();

    $response = $this->actingAs($pt)->getJson('/api/pt/exercises?access_tier=free');
    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.access_tier'))->toBe('free');
});
