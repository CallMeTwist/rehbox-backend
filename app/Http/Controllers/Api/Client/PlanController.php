<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // Get all of the client's plans, newest first
    public function myPlan(Request $request)
    {
        $client = $request->user()->client;

        if (! $client->isFree() && ! $client->isSubscribed()) {
            return response()->json([
                'message' => 'Subscribe to unlock your personalized plan.',
                'subscription_status' => $client->subscription_status,
            ], 402);
        }

        $lang = $client->language_preference ?? 'en';

        $plans = $client->exercisePlans()
            ->with(['exercises'])
            ->latest()
            ->get()
            ->each(function ($plan) use ($lang) {
                $plan->exercises->each(function ($exercise) use ($lang) {
                    $exercise->instructions = $exercise->getInstructionsForLanguage($lang);
                });
            });

        $activePlan = $plans->firstWhere('status', 'active');

        return response()->json([
            'plans' => $plans,
            'active_plan' => $activePlan,
            'plan' => $activePlan,
            'compliance_rate' => $activePlan?->compliance_rate ?? 0,
        ]);
    }
}
