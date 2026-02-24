<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $pt      = $request->user()->physiotherapist;
        $clients = $pt->clients()->with('user')->get();

        // Commission rate: 15% of each client's subscription
        $commissionRate = 0.15;

        $now = Carbon::now();

        // Monthly earnings from active subscriptions
        $monthlyEarnings = 0;
        $activeClients   = 0;

        $clientBreakdown = $clients->map(function ($client) use ($commissionRate, $now, &$monthlyEarnings, &$activeClients) {
            $latestSub = $client->subscriptions()
                ->where('status', 'active')
                ->latest()
                ->first();

            $commission = $latestSub
                ? round($latestSub->amount * $commissionRate, 2)
                : 0;

            if ($latestSub) {
                $monthlyEarnings += $commission;
                $activeClients++;
            }

            return [
                'client_name'       => $client->user->name,
                'subscription_plan' => $latestSub?->plan ?? 'none',
                'subscription_amount'=> $latestSub?->amount ?? 0,
                'your_commission'   => $commission,
                'status'            => $latestSub?->status ?? 'inactive',
            ];
        });

        // Last 6 months earning history
        $earningHistory = collect(range(5, 0))->map(function ($monthsAgo) use ($pt, $commissionRate) {
            $date  = Carbon::now()->subMonths($monthsAgo);
            $total = Subscription::whereHas('client', fn($q) =>
            $q->where('physiotherapist_id', $pt->id)
            )
                ->where('status', 'active')
                ->whereMonth('starts_at', $date->month)
                ->whereYear('starts_at', $date->year)
                ->sum('amount');

            return [
                'month'    => $date->format('M Y'),
                'earnings' => round($total * $commissionRate, 2),
            ];
        });

        return response()->json([
            'summary' => [
                'monthly_earnings'   => round($monthlyEarnings, 2),
                'active_clients'     => $activeClients,
                'total_clients'      => $clients->count(),
                'commission_rate'    => ($commissionRate * 100) . '%',
            ],
            'client_breakdown' => $clientBreakdown,
            'earning_history'  => $earningHistory,
        ]);
    }
}
