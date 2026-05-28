<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    // Initialize Paystack payment
    public function initialize(Request $request)
    {
        $data = $request->validate([
            'plan' => 'required|in:basic,standard,premium',
        ]);

        $amounts = [
            'basic' => 350000,  // ₦3,500 in kobo
            'standard' => \App\Models\AppSetting::getValue('standard_plan_price_kobo', 200000),
            'premium' => 2000000, // ₦20,000
        ];

        $client = $request->user()->client;
        $reference = 'RHB-'.strtoupper(Str::random(12));

        // Initialize transaction on Paystack
        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $request->user()->email,
                'amount' => $amounts[$data['plan']],
                'reference' => $reference,
                'callback_url' => config('app.frontend_url').'/client/home',
                'metadata' => [
                    'client_id' => $client->id,
                    'plan' => $data['plan'],
                ],
            ]);

        if (! $response->successful()) {
            return response()->json(['message' => 'Payment initialization failed.'], 500);
        }

        // Save pending subscription
        Subscription::create([
            'client_id' => $client->id,
            'paystack_reference' => $reference,
            'plan' => $data['plan'],
            'amount' => $amounts[$data['plan']] / 100,
            'status' => 'pending',
        ]);

        return response()->json([
            'authorization_url' => $response->json('data.authorization_url'),
            'reference' => $reference,
        ]);
    }

    // Paystack webhook — called by Paystack when payment succeeds
    public function webhook(Request $request)
    {
        // Verify signature
        $signature = $request->header('x-paystack-signature');
        $computed = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret'));

        if ($signature !== $computed) {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $event = $request->json('event');
        $data = $request->json('data');

        if ($event === 'charge.success') {
            $subscription = Subscription::where('paystack_reference', $data['reference'])->first();

            if ($subscription && $subscription->status === 'pending') {
                $subscription->update([
                    'status' => 'active',
                    'starts_at' => now(),
                    'expires_at' => now()->addMonth(),
                ]);

                // Activate client
                $subscription->client->update([
                    'subscription_status' => 'active',
                    'subscription_expires_at' => now()->addMonth(),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
