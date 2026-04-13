<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load(['client.physiotherapist.user', 'client.subscriptions']);
        $client = $user->client;

        $activeSub = $client->subscriptions()
            ->where('status', 'active')
            ->latest()
            ->first();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'client' => [
                'id' => $client->id,
                'phone' => $client->phone,
                'condition' => $client->condition,
                'coin_balance' => $client->coin_balance,
                'subscription_status' => $client->subscription_status,
                'language_preference' => $client->language_preference,
                'physiotherapist_id' => $client->physiotherapist_id,
                'physiotherapist' => $client->physiotherapist ? [
                    'id' => $client->physiotherapist->id,
                    'user_id' => $client->physiotherapist->user->id,
                    'name' => $client->physiotherapist->user->name,
                ] : null,
            ],
            'subscription' => $activeSub ? [
                'plan' => $activeSub->plan,
                'amount' => $activeSub->amount,
                'expires_at' => $activeSub->expires_at,
                'status' => $activeSub->status,
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $client = $user->client;

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
        }

        if (isset($data['phone'])) {
            $client->update(['phone' => $data['phone']]);
        }

        return response()->json(['message' => 'Profile updated.']);
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

    public function connectPT(Request $request)
    {
        $data = $request->validate([
            'activation_code' => 'required|string|exists:physiotherapists,activation_code',
        ]);

        $client = $request->user()->client;

        if ($client->physiotherapist_id) {
            return response()->json([
                'message' => 'You are already linked to a physiotherapist.',
            ], 422);
        }

        $pt = \App\Models\Physiotherapist::where('activation_code', $data['activation_code'])->first();

        $client->update(['physiotherapist_id' => $pt->id]);

        return response()->json([
            'message' => 'Successfully linked to '.$pt->user->name,
        ]);
    }
}
