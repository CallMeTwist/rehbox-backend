<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreSelfPlanRequest;
use App\Models\ExercisePlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SelfPlanController extends Controller
{
    public function store(StoreSelfPlanRequest $request): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');

        if ($client->isFree()) {
            $existing = ExercisePlan::query()
                ->where('created_by_client_id', $client->id)
                ->exists();

            if ($existing) {
                return response()->json([
                    'message' => 'Free plan limit reached. You can have only 1 plan — edit or delete it to make a new one.',
                ], 422);
            }
        }

        $plan = DB::transaction(function () use ($request, $client) {
            ExercisePlan::query()
                ->where('created_by_client_id', $client->id)
                ->where('status', 'active')
                ->update(['status' => 'completed']);

            $plan = ExercisePlan::create([
                'client_id' => $client->id,
                'created_by_client_id' => $client->id,
                'is_self_built' => true,
                'title' => $request->validated('title'),
                'status' => 'active',
                'start_date' => now(),
            ]);

            $this->syncExercises($plan, $request);

            return $plan;
        });

        return response()->json(['data' => $plan->load('exercises')], 201);
    }

    public function update(StoreSelfPlanRequest $request, ExercisePlan $plan): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');
        abort_if($plan->created_by_client_id !== $client->id, 403);

        $plan->update(['title' => $request->validated('title')]);
        $this->syncExercises($plan, $request);

        return response()->json(['data' => $plan->fresh('exercises')]);
    }

    public function destroy(Request $request, ExercisePlan $plan): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');
        abort_if($plan->created_by_client_id !== $client->id, 403);

        $plan->delete();

        return response()->json(['data' => ['deleted' => true]]);
    }

    private function syncExercises(ExercisePlan $plan, StoreSelfPlanRequest $request): void
    {
        $scheduledDays = $request->validated('scheduled_days');
        $sync = collect($request->validated('exercise_ids'))
            ->mapWithKeys(fn ($id, $idx) => [
                $id => [
                    'order' => $idx,
                    'sets' => 3,
                    'reps' => 10,
                    'scheduled_days' => json_encode($scheduledDays),
                ],
            ])->all();

        $plan->exercises()->sync($sync);
    }
}
