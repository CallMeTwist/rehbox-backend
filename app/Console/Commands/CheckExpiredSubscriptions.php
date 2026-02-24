<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    protected $signature   = 'subscriptions:check-expired';
    protected $description = 'Mark expired subscriptions and notify clients';

    public function handle(): void
    {
        // Find active subscriptions that have expired
        $expired = Subscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $sub) {
            $sub->update(['status' => 'expired']);

            $sub->client->update(['subscription_status' => 'expired']);

            // Send expiry notification email
            $sub->client->user->notify(new \App\Notifications\SubscriptionExpired());
        }

        $this->info("Processed {$expired->count()} expired subscriptions.");
    }
}
