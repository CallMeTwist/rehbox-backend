<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreReminderRequest;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');

        $reminders = Reminder::query()
            ->where('client_id', $client->id)
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->get();

        return response()->json(['data' => $reminders]);
    }

    public function store(StoreReminderRequest $request): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');

        if ($client->isFree() && $client->reminders()->count() >= 1) {
            abort(403, 'Upgrade to add more reminders.');
        }

        $reminder = Reminder::create([
            ...$request->validated(),
            'client_id' => $client->id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['data' => $reminder], 201);
    }

    public function update(StoreReminderRequest $request, Reminder $reminder): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null || $reminder->client_id !== $client->id, 403);

        $reminder->update($request->validated());

        return response()->json(['data' => $reminder]);
    }

    public function toggle(Request $request, Reminder $reminder): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null || $reminder->client_id !== $client->id, 403);

        $reminder->update(['is_active' => ! $reminder->is_active]);

        return response()->json(['data' => $reminder]);
    }

    public function destroy(Request $request, Reminder $reminder): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null || $reminder->client_id !== $client->id, 403);

        $reminder->delete();

        return response()->json(['data' => ['ok' => true]]);
    }
}
