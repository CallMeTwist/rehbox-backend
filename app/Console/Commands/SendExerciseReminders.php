<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Client\PushController;
use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendExerciseReminders extends Command
{
    protected $signature   = 'reminders:send';
    protected $description = 'Send exercise reminder push notifications';

    public function handle(): void
    {
        $now = Carbon::now()->format('H:i');

        // Find clients who haven't exercised today
        $clients = Client::whereHas('reminders', function ($q) {
            $q->where('is_active', true)
                ->whereJsonContains('times', Carbon::now()->format('H:i'));
        })
            ->whereDoesntHave('exerciseSessions', function ($q) {
                $q->whereDate('created_at', today())
                    ->where('status', 'completed');
            })
            ->with('user')
            ->get();

        foreach ($clients as $client) {
            PushController::sendToUser(
                $client->user_id,
                'Time to exercise! 🏃',
                'Your physiotherapist has a session waiting for you. Stay on track!'
            );
        }

        $this->info("Sent reminders to {$clients->count()} clients.");
    }
}
