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
        abort_if($client === null, 403, 'Client profile missing.');

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
            ->groupBy(fn ($e) => $e->category ?? 'other')
            ->map(fn ($group) => $group->map(
                fn ($e) => (new ExerciseResource($e))->toArray($request)
            )->values()->all())
            ->all();

        return response()->json(['data' => $grouped]);
    }
}
