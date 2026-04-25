<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseLibraryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = $request->user()->client;

        if ($client->isFree()) {
            $exercises = Exercise::query()
                ->where('is_personalized', false)
                ->orderBy('title')
                ->get();

            return response()->json(['data' => ExerciseResource::collection($exercises)]);
        }

        $grouped = Exercise::query()
            ->orderBy('title')
            ->get()
            ->groupBy('category')
            ->map(fn ($group) => ExerciseResource::collection($group));

        return response()->json(['data' => $grouped]);
    }
}
