<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->user()->client;

        if ($client === null || $client->isFree()) {
            return response()->json([
                'message' => 'Rewards are available on Standard. Upgrade to start earning coins.',
            ], 402);
        }

        $transactions = $client->coinTransactions()
            ->latest()
            ->paginate(20);

        return response()->json([
            'coin_balance' => $client->coin_balance,
            'transactions' => $transactions,
            'stats' => [
                'total_earned' => $client->coinTransactions()->where('type', 'earned')->sum('amount'),
                'total_spent' => abs($client->coinTransactions()->where('type', 'redeemed')->sum('amount')),
            ],
        ]);
    }
}
