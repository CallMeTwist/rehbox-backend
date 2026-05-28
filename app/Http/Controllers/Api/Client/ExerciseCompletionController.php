<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ExerciseCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseCompletionController extends Controller
{
    public function store(Request $request, Exercise $exercise): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');

        $completion = ExerciseCompletion::create([
            'client_id' => $client->id,
            'exercise_id' => $exercise->id,
            'completed_at' => now(),
        ]);

        if ($client->isFree()) {
            $client->awardCoins(50, "Generalized exercise: {$exercise->title}", $exercise);
        }

        return response()->json(['data' => $completion], 201);
    }
}
