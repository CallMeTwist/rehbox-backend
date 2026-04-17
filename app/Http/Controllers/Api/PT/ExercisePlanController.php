<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\ExercisePlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExercisePlanController extends Controller
{
    public function show(Request $request, ExercisePlan $plan): \Illuminate\Http\JsonResponse
    {
        $pt = $request->user()->physiotherapist;
        abort_unless($plan->physiotherapist_id === $pt->id, 403);

        return response()->json(['plan' => $plan->load('exercises')]);
    }

    // Create a new plan and assign to a client
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'duration_weeks' => 'integer|min:1|max:52',
            'frequency' => 'in:daily,alternate_days,custom',
            'reminder_times' => 'nullable|array',
            'reminder_times.*' => 'date_format:H:i',
            'start_date' => 'nullable|date|after_or_equal:today',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.hold_seconds' => 'integer|min:0',
            'exercises.*.pt_notes' => 'nullable|string',
        ]);

        $pt = $request->user()->physiotherapist;

        // Ensure client belongs to this PT
        $client = $pt->clients()->findOrFail($data['client_id']);

        $plan = ExercisePlan::create([
            'physiotherapist_id' => $pt->id,
            'client_id' => $client->id,
            'title' => $data['title'],
            'notes' => $data['notes'] ?? null,
            'duration_weeks' => $data['duration_weeks'] ?? 6,
            'frequency' => $data['frequency'] ?? 'daily',
            'reminder_times' => $data['reminder_times'] ?? [],
            'start_date' => $data['start_date'] ?? now()->toDateString(),
            'status' => 'active',
        ]);

        // Attach exercises with pivot data
        foreach ($data['exercises'] as $index => $ex) {
            $plan->exercises()->attach($ex['exercise_id'], [
                'order' => $index,
                'sets' => $ex['sets'],
                'reps' => $ex['reps'],
                'hold_seconds' => $ex['hold_seconds'] ?? 0,
                'pt_notes' => $ex['pt_notes'] ?? null,
            ]);
        }

        // Archive any previous active plan for this client (only one active at a time)
        $client->exercisePlans()
            ->where('status', 'active')
            ->where('id', '!=', $plan->id)
            ->update(['status' => 'completed']);

        // Notify client
        AppNotification::create([
            'user_id' => $client->user_id,
            'type' => 'plan_assigned',
            'title' => 'New exercise plan assigned',
            'body' => "Your physiotherapist created a new plan: {$plan->title}",
            'data' => ['plan_id' => $plan->id],
        ]);

        return response()->json([
            'message' => 'Plan created and assigned to client.',
            'plan' => $plan->load('exercises'),
        ], 201);
    }

    // Update an existing plan
    public function update(Request $request, ExercisePlan $plan): JsonResponse
    {
        $pt = $request->user()->physiotherapist;

        // Ensure plan belongs to this PT
        if ($plan->physiotherapist_id !== $pt->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'in:draft,active,completed,paused',
            'reminder_times' => 'nullable|array',
            'exercises' => 'sometimes|array|min:1',
            'exercises.*.exercise_id' => 'required_with:exercises|exists:exercises,id',
            'exercises.*.sets' => 'required_with:exercises|integer|min:1',
            'exercises.*.reps' => 'required_with:exercises|integer|min:1',
            'exercises.*.hold_seconds' => 'integer|min:0',
            'exercises.*.pt_notes' => 'nullable|string',
        ]);

        $plan->update($data);

        if (isset($data['exercises'])) {
            $plan->exercises()->detach();
            foreach ($data['exercises'] as $index => $ex) {
                $plan->exercises()->attach($ex['exercise_id'], [
                    'order' => $index,
                    'sets' => $ex['sets'],
                    'reps' => $ex['reps'],
                    'hold_seconds' => $ex['hold_seconds'] ?? 0,
                    'pt_notes' => $ex['pt_notes'] ?? null,
                ]);
            }
        }

        // Notify client of the update (after exercise sync so client sees updated data)
        AppNotification::create([
            'user_id' => $plan->client->user_id,
            'type' => 'plan_updated',
            'title' => 'Exercise plan updated',
            'body' => "Your physiotherapist updated your plan: {$plan->title}",
            'data' => [
                'plan_id' => $plan->id,
                'changes' => array_keys($data),
            ],
        ]);

        return response()->json([
            'message' => 'Plan updated.',
            'plan' => $plan->load('exercises'),
        ]);
    }

    public function destroy(Request $request, ExercisePlan $plan): JsonResponse
    {
        $pt = $request->user()->physiotherapist;
        if ($plan->physiotherapist_id !== $pt->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $plan->delete();

        return response()->json(['message' => 'Plan deleted.']);
    }
}
