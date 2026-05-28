<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\ExerciseCompletion;
use App\Models\ExerciseSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');

        if ($client->isFree()) {
            return response()->json($this->freeStreakPayload($client));
        }

        $now = Carbon::now();

        // Sessions this month
        $monthlySessions = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereMonth('completed_at', $now->month)
            ->whereYear('completed_at', $now->year)
            ->with('exercise:id,title,category')
            ->get();

        // Last 8 weeks for the chart
        $weeklyData = collect(range(7, 0))->map(function ($weeksAgo) use ($client) {
            $start = Carbon::now()->subWeeks($weeksAgo)->startOfWeek();
            $end = Carbon::now()->subWeeks($weeksAgo)->endOfWeek();
            $count = ExerciseSession::where('client_id', $client->id)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$start, $end])
                ->count();

            return [
                'week' => $start->format('d M'),
                'sessions' => $count,
            ];
        });

        // Current streak (consecutive days with at least one session)
        $streak = $this->calculateStreak($client->id);

        // Average form score
        $avgFormScore = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereNotNull('form_score')
            ->avg('form_score');

        // Most exercised body area
        $topCategory = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->join('exercises', 'exercise_sessions.exercise_id', '=', 'exercises.id')
            ->selectRaw('exercises.category, COUNT(*) as count')
            ->groupBy('exercises.category')
            ->orderByDesc('count')
            ->first();

        // Monthly summary stats
        $totalCoinsEarned = $client->coinTransactions()
            ->where('type', 'earned')
            ->whereMonth('created_at', $now->month)
            ->sum('amount');

        return response()->json([
            'summary' => [
                'total_sessions_this_month' => $monthlySessions->count(),
                'current_streak_days' => $streak,
                'avg_form_score' => round($avgFormScore ?? 0),
                'coins_earned_this_month' => $totalCoinsEarned,
                'top_category' => $topCategory?->category,
            ],
            'weekly_chart' => $weeklyData,
            'recent_sessions' => $monthlySessions->take(10)->map(fn ($s) => [
                'id' => $s->id,
                'exercise' => $s->exercise?->title,
                'category' => $s->exercise?->category,
                'form_score' => $s->form_score,
                'coins_earned' => $s->coins_earned,
                'completed_at' => $s->completed_at,
            ]),
        ]);
    }

    // Monthly PDF/data report
    public function monthlyReport(Request $request, int $month, int $year)
    {
        $client = $request->user()->client;
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $sessions = ExerciseSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->with(['exercise:id,title,category', 'plan:id,title'])
            ->get();

        $plan = $client->exercisePlans()
            ->where('status', 'active')
            ->first();

        return response()->json([
            'period' => $start->format('F Y'),
            'client_name' => $client->user->name,
            'plan_title' => $plan?->title,
            'pt_name' => $client->physiotherapist?->user->name,
            'total_sessions' => $sessions->count(),
            'avg_form_score' => round($sessions->avg('form_score') ?? 0),
            'total_coins' => $sessions->sum('coins_earned'),
            'compliance_rate' => $plan?->compliance_rate ?? 0,
            'sessions' => $sessions,
        ]);
    }

    private function calculateStreak(int $clientId): int
    {
        $streak = 0;
        $date = Carbon::today();

        while (true) {
            $hasSession = ExerciseSession::where('client_id', $clientId)
                ->where('status', 'completed')
                ->whereDate('completed_at', $date)
                ->exists();

            if (! $hasSession) {
                break;
            }

            $streak++;
            $date->subDay();
        }

        return $streak;
    }

    /**
     * @return array{current_streak: int, longest_streak: int, last_7_days: list<bool>}
     */
    private function freeStreakPayload(\App\Models\Client $client): array
    {
        $tz = 'Africa/Lagos';
        $today = now($tz)->startOfDay();

        $sessionDates = $client->exerciseSessions()
            ->whereNotNull('completed_at')
            ->pluck('completed_at');

        $completionDates = ExerciseCompletion::query()
            ->where('client_id', $client->id)
            ->pluck('completed_at');

        $allDates = $sessionDates->merge($completionDates)
            ->map(fn ($ts) => Carbon::parse($ts)->setTimezone($tz)->startOfDay()->toDateString())
            ->unique()
            ->sort()
            ->values();

        $current = 0;
        $cursor = $today->copy();
        while ($allDates->contains($cursor->toDateString())) {
            $current++;
            $cursor->subDay();
        }

        $longest = 0;
        $run = 0;
        $prev = null;
        foreach ($allDates as $d) {
            $date = Carbon::parse($d, $tz);
            if ($prev !== null && $date->copy()->subDay()->toDateString() === $prev) {
                $run++;
            } else {
                $run = 1;
            }
            $longest = max($longest, $run);
            $prev = $d;
        }

        $last7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7[] = $allDates->contains($today->copy()->subDays($i)->toDateString());
        }

        return [
            'current_streak' => $current,
            'longest_streak' => $longest,
            'last_7_days' => $last7,
        ];
    }
}
