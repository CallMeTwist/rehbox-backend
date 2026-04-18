<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use App\Models\ExerciseSession;
use Illuminate\Http\Request;

class MotionReportController extends Controller
{
    /**
     * All completed sessions for a specific client, with ROM trend data.
     *
     * Returns:
     *  - form score trend (existing)
     *  - ROM trend: per-session achieved ROM extracted from motion_data.rep_history
     *  - weekly compliance data for the Recovery Progress chart
     */
    public function clientReports(Request $request, int $clientId): \Illuminate\Http\JsonResponse
    {
        $pt = $request->user()->physiotherapist;
        $client = $pt->clients()->findOrFail($clientId);

        $sessions = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->with('exercise:id,title,category,correct_angles')
            ->latest()
            ->paginate(15);

        $avgFormScore = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereNotNull('form_score')
            ->avg('form_score');

        // Form score trend — last 10 sessions, oldest first
        $trendSessions = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereNotNull('form_score')
            ->latest()
            ->take(10)
            ->get(['id', 'form_score', 'completed_at', 'exercise_id', 'motion_data'])
            ->reverse()
            ->values();

        $trend = $trendSessions->map(function (ExerciseSession $s) {
            $repHistory = $s->motion_data['rep_history'] ?? [];
            $avgRom = $this->avgRom($repHistory);
            $bestRom = $this->bestRom($repHistory);
            $repCount = $s->motion_data['rep_count'] ?? count($repHistory);

            return [
                'id' => $s->id,
                'form_score' => $s->form_score,
                'completed_at' => $s->completed_at,
                'exercise_id' => $s->exercise_id,
                'avg_rom' => $avgRom !== null ? round($avgRom, 1) : null,
                'best_rom' => $bestRom !== null ? round($bestRom, 1) : null,
                'rep_count' => $repCount,
            ];
        });

        // Weekly compliance chart — last 8 weeks
        // A session counts as "compliant" if status=completed on that week.
        // We also pull average form score and average ROM per week.
        $weeklyData = collect(range(7, 0))->map(function (int $weeksAgo) use ($client) {
            $start = now()->subWeeks($weeksAgo)->startOfWeek();
            $end = now()->subWeeks($weeksAgo)->endOfWeek();

            $weeklySessions = ExerciseSession::where('client_id', $client->id)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$start, $end])
                ->get(['form_score', 'motion_data', 'completed_at']);

            $sessionCount = $weeklySessions->count();
            $avgFormScore = $weeklySessions->avg('form_score');

            // Extract best ROM from each session's motion_data, average across the week
            $weeklyRoms = $weeklySessions
                ->map(fn ($s) => $this->bestRom($s->motion_data['rep_history'] ?? []))
                ->filter(fn ($v) => $v !== null)
                ->values();

            $avgWeeklyRom = $weeklyRoms->count() > 0
                ? round($weeklyRoms->avg(), 1)
                : null;

            return [
                'week' => $start->format('d M'),
                'sessions' => $sessionCount,
                'avg_form' => $avgFormScore ? round($avgFormScore) : null,
                'avg_rom' => $avgWeeklyRom,
            ];
        });

        return response()->json([
            'client_name' => $client->user->name,
            'avg_form_score' => round($avgFormScore ?? 0),
            'trend' => $trend,
            'weekly_data' => $weeklyData,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Single session detail with full motion data and per-rep ROM breakdown.
     */
    public function sessionDetail(Request $request, int $sessionId): \Illuminate\Http\JsonResponse
    {
        $pt = $request->user()->physiotherapist;
        $session = ExerciseSession::with(['exercise', 'client.user'])
            ->findOrFail($sessionId);

        if ($session->client->physiotherapist_id !== $pt->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $repHistory = $session->motion_data['rep_history'] ?? [];

        return response()->json([
            ...$session->toArray(),
            'rom_summary' => [
                'rep_count' => $session->motion_data['rep_count'] ?? count($repHistory),
                'avg_rom' => $this->avgRom($repHistory),
                'best_rom' => $this->bestRom($repHistory),
                'per_rep' => $repHistory,
            ],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /** Average peak angle (max) across completed reps — average achieved ROM. */
    private function avgRom(array $repHistory): ?float
    {
        if (empty($repHistory)) {
            return null;
        }

        $peaks = array_filter(
            array_column($repHistory, 'max'),
            fn ($v) => $v !== null,
        );

        return empty($peaks) ? null : array_sum($peaks) / count($peaks);
    }

    /** Best (maximum) angle achieved across all reps in the session. */
    private function bestRom(array $repHistory): ?float
    {
        if (empty($repHistory)) {
            return null;
        }

        $peaks = array_filter(
            array_column($repHistory, 'max'),
            fn ($v) => $v !== null,
        );

        return empty($peaks) ? null : max($peaks);
    }
}
