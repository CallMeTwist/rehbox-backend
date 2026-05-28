<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlanController extends Controller
{
    // Get all of the client's plans, newest first
    public function myPlan(Request $request)
    {
        $client = $request->user()->client;

        if ($client->isFree()) {
            return response()->json([
                'plan' => null,
                'locked' => true,
                'reason' => 'free_tier',
            ]);
        }

        if (! $client->isSubscribed()) {
            return response()->json([
                'message' => 'Subscribe to unlock your personalized plan.',
                'subscription_status' => $client->subscription_status,
            ], 402);
        }

        $lang = $client->language_preference ?? 'en';

        $disk = Storage::disk(config('rehbox.exercise_video_disk'));

        $plans = $client->exercisePlans()
            ->with(['exercises'])
            ->latest()
            ->get()
            ->each(function ($plan) use ($lang, $disk) {
                $plan->exercises->each(function ($exercise) use ($lang, $disk) {
                    $exercise->instructions = $exercise->getInstructionsForLanguage($lang);

                    $exercise->thumbnail_url = $exercise->illustration_url
                        ?? ($exercise->video_source === 'youtube' && $exercise->video_path
                            ? "https://i.ytimg.com/vi/{$exercise->video_path}/hqdefault.jpg"
                            : ($exercise->thumbnail_path ? $disk->url($exercise->thumbnail_path) : null));

                    $exercise->video_url = ($exercise->video_source === 'upload' && $exercise->video_path)
                        ? $disk->url($exercise->video_path)
                        : null;
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
