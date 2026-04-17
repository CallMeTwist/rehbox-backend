<?php

namespace App\Http\Controllers\Api\Client;

use App\Events\ClientCompletedExercise;
use App\Events\ClientStartedExercise;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\ExercisePlan;
use App\Models\ExerciseSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    // Called when client taps "Start" on an exercise
    public function start(Request $request)
    {
        $data = $request->validate([
            'exercise_plan_id' => 'required|exists:exercise_plans,id',
            'exercise_id' => 'required|exists:exercises,id',
        ]);

        $client = $request->user()->client;

        $session = ExerciseSession::create([
            'client_id' => $client->id,
            'exercise_plan_id' => $data['exercise_plan_id'],
            'exercise_id' => $data['exercise_id'],
            'started_at' => now(),
            'status' => 'started',
        ]);

        // Notify PT in real time
        $plan = ExercisePlan::find($data['exercise_plan_id']);
        event(new ClientStartedExercise($client, $plan, $session));

        return response()->json(['session_id' => $session->id], 201);
    }

    // Called when client finishes — submits motion data + earns coin
    public function complete(Request $request, ExerciseSession $session): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'motion_data' => 'nullable|array',
            'form_score' => 'nullable|integer|min:0|max:100',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $client = $request->user()->client;

        if ($session->client_id !== $client->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $formScore = $data['form_score'] ?? 50;
        $coinsEarned = $this->calculateCoins($formScore);

        $session->update([
            'completed_at' => now(),
            'status' => 'completed',
            'motion_data' => $data['motion_data'] ?? null,
            'form_score' => $formScore,
            'rating' => $data['rating'] ?? null,
            'coins_earned' => $coinsEarned,
        ]);

        $session->load('exercise');
        $exerciseTitle = $session->exercise?->title ?? 'exercise';

        // Award coins AND create a CoinTransaction record
        $client->awardCoins($coinsEarned, "Completed: {$exerciseTitle}", $session);

        // Notify PT if plan exists
        $plan = ExercisePlan::with('physiotherapist')->find($session->exercise_plan_id);
        if ($plan?->physiotherapist) {
            AppNotification::create([
                'user_id' => $plan->physiotherapist->user_id,
                'type' => 'session_completed',
                'title' => 'Client completed a session',
                'body' => "{$client->user->name} completed {$exerciseTitle} — Form score: {$formScore}%",
                'data' => [
                    'client_id' => $client->id,
                    'session_id' => $session->id,
                    'form_score' => $formScore,
                ],
            ]);
        }

        // Notify PT via event
        event(new ClientCompletedExercise($client, $plan, $session));

        return response()->json([
            'message' => 'Session completed!',
            'coins_earned' => $coinsEarned,
            'new_balance' => $client->fresh()->coin_balance,
            'form_score' => $formScore,
        ]);
    }

    // Coin calculation — bonus for good form
    private function calculateCoins(int $formScore): int
    {
        if ($formScore >= 80) {
            return 3;
        }  // Great form
        if ($formScore >= 50) {
            return 2;
        }  // Good form

        return 1;                         // Completed regardless
    }

    // Get session history for a client
    public function history(Request $request)
    {
        $client = $request->user()->client;
        $sessions = ExerciseSession::where('client_id', $client->id)
            ->with(['exercise:id,title,category', 'plan:id,title'])
            ->where('status', 'completed')
            ->latest()
            ->paginate(20);

        return response()->json($sessions);
    }
}
