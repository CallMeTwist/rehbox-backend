<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string',
            'public_key' => 'required|string',
            'auth_token' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json(['message' => 'Subscribed to push notifications.']);
    }

    public function unsubscribe(Request $request)
    {
        PushSubscription::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Unsubscribed.']);
    }

    // Called by the reminder scheduler
    public static function sendToUser(int $userId, string $title, string $body): void
    {
        $sub = PushSubscription::where('user_id', $userId)->first();
        if (! $sub) {
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => config('services.vapid.public'),
                'privateKey' => config('services.vapid.private'),
            ],
        ]);

        $webPush->queueNotification(
            Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
            ]),
            json_encode(['title' => $title, 'body' => $body, 'icon' => '/icons/pwa-192x192.png'])
        );

        $webPush->flush();
    }
}
