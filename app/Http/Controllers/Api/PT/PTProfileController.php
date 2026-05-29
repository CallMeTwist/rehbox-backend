<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PTProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $pt = $user->physiotherapist;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
            ],
            'physiotherapist' => [
                'id' => $pt->id,
                'license_number' => $pt->license_number,
                'hospital_or_clinic' => $pt->hospital_or_clinic,
                'specialty' => $pt->specialty,
                'city' => $pt->city,
                'bio' => $pt->bio,
                'vetting_status' => $pt->vetting_status,
                'activation_code' => $pt->activation_code,
                'coin_balance' => $pt->coin_balance ?? 0,
                'client_count' => $pt->clients()->count(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $pt = $user->physiotherapist;

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'specialty' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'hospital_or_clinic' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string|max:1000',
        ]);

        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
            unset($data['name']);
        }

        if (! empty($data)) {
            $pt->update($data);
        }

        return response()->json(['message' => 'Profile updated.']);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:5120',
        ]);

        $user = $request->user();

        if ($user->avatar_url) {
            $old = ltrim(str_replace('/storage/', '', $user->avatar_url), '/');
            \Storage::disk('public')->delete($old);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $url = '/storage/'.$path;

        $user->update(['avatar_url' => $url]);

        return response()->json(['avatar_url' => $url]);
    }
}
