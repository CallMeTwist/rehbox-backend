<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user   = $request->user();
        $client = $user->client->load(['physiotherapist.user']);

        return response()->json([
            'user'   => $user->only(['id', 'name', 'email', 'role']),
            'client' => $client,
        ]);
    }

    public function updateLanguage(Request $request)
    {
        $data = $request->validate([
            'language' => 'required|in:en,pcm,yo,ig,ha',
        ]);

        $request->user()->client->update([
            'language_preference' => $data['language'],
        ]);

        return response()->json(['message' => 'Language updated.']);
    }
}
