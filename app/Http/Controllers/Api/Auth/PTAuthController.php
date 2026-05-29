<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Physiotherapist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PTAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'required|string',
            'license_number' => 'required|string',
            'hospital_or_clinic' => 'nullable|string',
            'specialty' => 'nullable|string',
            'city' => 'nullable|string',
            'credential_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'agreed_to_terms' => 'required|accepted',
        ]);

        // Store credential document
        $docPath = $request->hasFile('credential_document')
            ? $request->file('credential_document')->store('credentials', 'private')
            : null;

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'pt',
        ]);

        // Create physiotherapist profile
        Physiotherapist::create([
            'user_id' => $user->id,
            'license_number' => $data['license_number'],
            'hospital_or_clinic' => $data['hospital_or_clinic'] ?? null,
            'specialty' => $data['specialty'] ?? null,
            'phone' => $data['phone'],
            'city' => $data['city'] ?? null,
            'credential_document_path' => $docPath,
            'vetting_status' => 'pending',
        ]);

        $token = $user->createToken('pt-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Your account is under review (up to 48 hours).',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'vetting_status' => 'pending',
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])
            ->where('role', 'pt')
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $pt = $user->physiotherapist;
        $token = $user->createToken('pt-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar_url' => $user->avatar_url,
                'vetting_status' => $pt?->vetting_status,
                'activation_code' => $pt?->activation_code,
            ],
        ]);
    }
}
