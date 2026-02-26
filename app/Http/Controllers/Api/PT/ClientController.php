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
                    'id'                  => $client->id,
                    'name'                => $client->user->name,
                    'email'               => $client->user->email,
                    'phone'               => $client->phone,
                    'primary_condition'   => $client->primary_condition,
                    'subscription_status' => $client->subscription_status,
                    'coin_balance'        => $client->coin_balance,
                    'compliance_rate'     => $activePlan?->compliance_rate ?? 0,
                    'active_plan_title'   => $activePlan?->title,
                    'last_session'        => $activePlan?->sessions
                        ->where('status', 'completed')
                        ->sortByDesc('completed_at')
                        ->first()?->completed_at,
                ];
            });

        return response()->json([
            'clients' => $clients,
            'total'   => $clients->count(),
            'slots_remaining' => 5 - $clients->count(),
        ]);
    }

    // Get a single client's full detail
    public function show(Request $request, int $clientId)
    {
        $pt     = $request->user()->physiotherapist;
        $client = $pt->clients()
            ->with(['user', 'exercisePlans.exercises', 'exercisePlans.sessions'])
            ->findOrFail($clientId);

        return response()->json($client);
    }

    public function updateCondition(Request $request, int $clientId)
    {
        $pt     = $request->user()->physiotherapist;
        $client = $pt->clients()->findOrFail($clientId);

        $data = $request->validate([
            'condition' => 'required|string|max:255',
        ]);

        $client->update(['condition' => $data['condition']]);

        return response()->json(['message' => 'Condition updated.', 'condition' => $client->condition]);
    }
}
