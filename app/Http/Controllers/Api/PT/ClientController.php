<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Get all clients for the authenticated PT
    public function index(Request $request)
    {
        $pt = $request->user()->physiotherapist;

        $clients = $pt->clients()
            ->with(['user', 'exercisePlans' => function ($q) {
                $q->where('status', 'active')->with('sessions');
            }])
            ->get()
            ->map(function ($client) {
                $activePlan = $client->exercisePlans->first();

                return [
                    'id' => $client->id,
                    'user_id' => $client->user->id,
                    'name' => $client->user->name,
                    'email' => $client->user->email,
                    'phone' => $client->phone,
                    'primary_condition' => $client->primary_condition,
                    'subscription_status' => $client->subscription_status,
                    'coin_balance' => $client->coin_balance,
                    'compliance_rate' => $activePlan?->compliance_rate ?? 0,
                    'active_plan_title' => $activePlan?->title,
                    'last_session' => $activePlan?->sessions
                        ->where('status', 'completed')
                        ->sortByDesc('completed_at')
                        ->first()?->completed_at,
                ];
            });

        return response()->json([
            'clients' => $clients,
            'total' => $clients->count(),
        ]);
    }

    // Get a single client's full detail
    public function show(Request $request, int $clientId)
    {
        $pt = $request->user()->physiotherapist;
        $client = $pt->clients()
            ->with([
                'user',
                'exercisePlans.exercises',
                'exercisePlans.sessions.exercise',
            ])
            ->findOrFail($clientId);

        // Calculate compliance
        $totalSessions = $client->exerciseSessions()->where('status', 'completed')->count();
        $daysWithSession = $client->exerciseSessions()
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->selectRaw('DATE(completed_at) as day')
            ->distinct()->count();
        $compliance = now()->daysInMonth > 0
            ? round(($daysWithSession / now()->daysInMonth) * 100)
            : 0;

        $client->compliance_rate = min($compliance, 100);

        return response()->json($client);
    }

    public function updateCondition(Request $request, int $clientId)
    {
        $pt = $request->user()->physiotherapist;
        $client = $pt->clients()->findOrFail($clientId);

        $data = $request->validate([
            'condition' => 'required|string|max:255',
        ]);

        $client->update(['condition' => $data['condition']]);

        return response()->json(['message' => 'Condition updated.', 'condition' => $client->condition]);
    }
}
