<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use App\Models\ExerciseSession;
use Illuminate\Http\Request;

class MotionReportController extends Controller
{
    // All sessions for a specific client
    public function clientReports(Request $request, int $clientId)
    {
        $pt     = $request->user()->physiotherapist;
        $client = $pt->clients()->findOrFail($clientId);

        $sessions = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->with('exercise:id,title,category')
            ->latest()
            ->paginate(15);

        $avgFormScore = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereNotNull('form_score')
            ->avg('form_score');

        // Form score trend over last 10 sessions
        $trend = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereNotNull('form_score')
            ->latest()
            ->take(10)
            ->get(['id', 'form_score', 'completed_at', 'exercise_id'])
            ->reverse()
            ->values();

        return response()->json([
            'client_name'   => $client->user->name,
            'avg_form_score'=> round($avgFormScore ?? 0),
            'trend'         => $trend,
            'sessions'      => $sessions,
        ]);
    }

    // Single session detail with full motion data
    public function sessionDetail(Request $request, int $sessionId)
    {
        $pt      = $request->user()->physiotherapist;
        $session = ExerciseSession::with(['exercise', 'client.user'])
            ->findOrFail($sessionId);

        // Ensure PT owns this client
        if ($session->client->physiotherapist_id !== $pt->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($session);
    }
}
