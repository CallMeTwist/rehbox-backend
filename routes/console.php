<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// routes/console.php
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Schedule;

Schedule::command('subscriptions:check-expired')->daily();

Broadcast::channel('online', function ($user) {
    // Any authenticated user can join the online presence channel
    return ['id' => $user->id, 'name' => $user->name];
});
