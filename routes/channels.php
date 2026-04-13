<?php

use Illuminate\Support\Facades\Broadcast;

// Any authenticated user joins the online presence channel
Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name, 'role' => $user->role];
});

// Private chat channel per client
Broadcast::channel('chat.{clientId}', function ($user, $clientId) {
    if ($user->role === 'pt') {
        return $user->physiotherapist?->clients()->where('id', $clientId)->exists();
    }

    return $user->client?->id == $clientId;
});
