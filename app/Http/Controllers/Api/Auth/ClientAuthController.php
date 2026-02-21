<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Physiotherapist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|confirmed|min:8',
            'phone'           => 'nullable|string',
            'activation_code' => 'required|string|exists:physiotherapists,activation_code',
            'agreed_to_terms' => 'required|accepted',
        ]);

        // Find the PT via activation code
        $pt = Physiotherapist::where('activation_code', $data['activation_code'])->firstOrFail();

        // Enforce max 5 clients per PT
        if ($pt->clients()->count() >= 5) {
            return response()->json([
                'message' => 'This physiotherapist has reached their maximum client capacity (5).',
            ], 422);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'client',
        ]);

        Client::create([
            'user_id'              => $user->id,
            'physiotherapist_id'   => $pt->id,
            'phone'                => $data['phone'] ?? null,
            'subscription_status'  => 'inactive',
        ]);

        $token = $user->createToken('client-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful! Subscribe to unlock your personalized plan.',
            'token'   => $token,
            'user'    => [
                'id'                  => $user->id,
                'name'                => $user->name,
                'email'               => $user->email,
                'role'                => $user->role,
                'subscription_status' => 'inactive',
                'pt_name'             => $pt->user->name,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])
            ->where('role', 'client')
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $client = $user->client;
        $token  = $user->createToken('client-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'                  => $user->id,
                'name'                => $user->name,
                'email'               => $user->email,
                'role'                => $user->role,
                'subscription_status' => $client?->subscription_status,
                'coin_balance'        => $client?->coin_balance,
                'language_preference' => $client?->language_preference,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }
}
