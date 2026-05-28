<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Client\PushController;
use App\Models\Reminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendExerciseReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Send exercise reminder push notifications';

    public function handle(): void
    {
        $messages = [
            'exercise' => ['Time to exercise! 🏃',  'Your physiotherapist has a session ready. Stay on track!'],
            'posture' => ['Posture check! 🧍',      'Take a moment to straighten up and adjust your posture.'],
            'hydration' => ['Stay hydrated! 💧',      'Drink a glass of water — your body will thank you.'],
            'diet' => ['Meal reminder! 🥗',       "Time to follow your nutrition plan. You've got this!"],
        ];

        $now = Carbon::now()->format('H:i');
        $today = strtolower(now()->englishDayOfWeek);

        $reminders = Reminder::query()
            ->where('is_active', true)
            ->whereJsonContains('times', $now)
            ->whereJsonContains('days', $today)
            ->with('client.user')
            ->get();

        $count = 0;

        foreach ($reminders as $reminder) {
            $client = $reminder->client;

            if (! $client) {
                continue;
            }

            if ($reminder->type === 'exercise') {
                $completedToday = $client->exerciseSessions()
                    ->whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->exists();

                if ($completedToday) {
                    continue;
                }
            }

            [$title, $body] = $messages[$reminder->type] ?? $messages['exercise'];

            PushController::sendToUser($client->user_id, $title, $body);

            $count++;
        }

        $this->info("Sent {$count} push notifications.");
    }
}
