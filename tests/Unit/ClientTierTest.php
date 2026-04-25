<?php

use App\Models\Client;

it('reports free tier correctly', function () {
    $client = new Client(['subscription_plan' => 'free']);
    expect($client->isFree())->toBeTrue();
    expect($client->isPaid())->toBeFalse();
});

it('reports standard tier correctly', function () {
    $client = new Client(['subscription_plan' => 'standard']);
    expect($client->isFree())->toBeFalse();
    expect($client->isPaid())->toBeTrue();
});
