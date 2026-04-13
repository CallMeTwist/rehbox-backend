<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use App\Models\ExerciseSession;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $pt = $request->user()->physiotherapist;
        $clients = $pt->clients()->with(['user', 'subscriptions', 'exerciseSessions'])->get();

        // Compliance per client (sessions this month)
        $clientStats = $clients->map(function ($client) {
            $totalSessions = $client->exerciseSessions()->where('status', 'completed')->count();
            $thisMonthSessions = $client->exerciseSessions()
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->count();

            $activeSub = $client->subscriptions()->where('status', 'active')->latest()->first();

            // Compliance = sessions this month / (plan frequency * weeks in month) * 100
            // Simple version: percentage of days with sessions this month
            $daysThisMonth = now()->daysInMonth;
            $daysWithSession = $client->exerciseSessions()
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->selectRaw('DATE(completed_at) as day')
                ->distinct()
                ->count();

            $compliance = $daysThisMonth > 0
                ? round(($daysWithSession / $daysThisMonth) * 100)
                : 0;

            return [
                'id' => $client->id,
                'name' => $client->user->name,
                'condition' => $client->condition ?? 'Not specified',
                'compliance' => min($compliance, 100),
                'total_sessions' => $totalSessions,
                'subscription' => $activeSub?->plan ?? 'none',
                'last_session' => $client->exerciseSessions()
                    ->where('status', 'completed')
                    ->latest('completed_at')
                    ->value('completed_at'),
            ];
        });

        // Average compliance across all clients
        $avgCompliance = $clientStats->avg('compliance');

        // Monthly compliance chart (last 5 months)
        // Compliance = (distinct days with at least one completed session) / (days in month) * 100
        $complianceChart = collect(range(4, 0))->map(function ($monthsAgo) use ($pt) {
            $date = Carbon::now()->subMonths($monthsAgo);

            $daysWithSession = ExerciseSession::whereHas('client', fn ($q) => $q->where('physiotherapist_id', $pt->id)
            )
                ->where('status', 'completed')
                ->whereMonth('completed_at', $date->month)
                ->whereYear('completed_at', $date->year)
                ->selectRaw('DATE(completed_at) as day')
                ->distinct()
                ->count();

            $compliance = $date->daysInMonth > 0
                ? round(($daysWithSession / $date->daysInMonth) * 100)
                : 0;

            return [
                'month' => $date->format('M'),
                'compliance' => min($compliance, 100),
            ];
        });

        // Earnings
        $commissionRate = 0.15;
        $activeSubs = Subscription::whereHas('client', fn ($q) => $q->where('physiotherapist_id', $pt->id)
        )
            ->where('status', 'active')
            ->get();

        $monthlyRevenue = $activeSubs->sum('amount');
        $commissionEarned = round($monthlyRevenue * $commissionRate);

        // Total sessions across all clients
        $totalSessions = ExerciseSession::whereHas('client', fn ($q) => $q->where('physiotherapist_id', $pt->id)
        )
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'stats' => [
                'total_clients' => $clients->count(),
                'active_clients' => $activeSubs->count(),
                'avg_compliance' => round($avgCompliance ?? 0),
                'plans_created' => $pt->exercisePlans()->count(),
                'monthly_revenue' => $monthlyRevenue,
                'commission_earned' => $commissionEarned,
                'total_sessions' => $totalSessions,
            ],
            'compliance_chart' => $complianceChart,
            'recent_clients' => $clientStats->sortByDesc('last_session')->take(4)->values(),
        ]);
    }
}
