<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // Get the client's active personalized plan
    public function myPlan(Request $request)
    {
        $client = $request->user()->client;

        if (!$client->isSubscribed()) {
            return response()->json([
                'message'             => 'Subscribe to unlock your personalized plan.',
                'subscription_status' => $client->subscription_status,
            ], 402);
        }

        $plan = $client->exercisePlans()
            ->with(['exercises', 'sessions' => function ($q) use ($client) {
                $q->where('client_id', $client->id)->latest()->limit(50);
            }])
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$plan) {
            return response()->json([
                'message' => 'No active plan yet. Your physiotherapist will assign one shortly.',
                'plan'    => null,
            ]);
        }

        // Add language-specific instructions to each exercise
        $lang = $client->language_preference ?? 'en';
        $plan->exercises->each(function ($exercise) use ($lang) {
            $exercise->instructions = $exercise->getInstructionsForLanguage($lang);
        });

        return response()->json([
            'plan'            => $plan,
            'compliance_rate' => $plan->compliance_rate,
        ]);
    }
}
