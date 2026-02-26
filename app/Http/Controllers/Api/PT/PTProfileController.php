<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PTProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $pt   = $user->physiotherapist;

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'physiotherapist' => [
                'id'              => $pt->id,
                'license_number'  => $pt->license_number,
                'hospital_or_clinic' => $pt->hospital_or_clinic,
                'specialty'       => $pt->specialty,
                'city'            => $pt->city,
                'bio'             => $pt->bio,
                'vetting_status'  => $pt->vetting_status,
                'activation_code' => $pt->activation_code,
                'coin_balance'    => $pt->coin_balance ?? 0,
                'client_count'    => $pt->clients()->count(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $pt   = $user->physiotherapist;

        $data = $request->validate([
            'name'             => 'sometimes|string|max:255',
            'specialty'        => 'sometimes|string|max:255',
            'city'             => 'sometimes|string|max:255',
            'hospital_or_clinic' => 'sometimes|string|max:255',
            'bio'              => 'sometimes|string|max:1000',
        ]);

        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
            unset($data['name']);
        }

        if (!empty($data)) {
            $pt->update($data);
        }

        return response()->json(['message' => 'Profile updated.']);
    }
}
