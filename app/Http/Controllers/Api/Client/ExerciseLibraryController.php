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

        $query = Exercise::query()
            ->where('is_active', true)
            ->where('is_personalized', false)
            ->orderBy('area')
            ->orderBy('category')
            ->orderBy('title');

        if ($area = $request->query('area')) {
            $query->where('area', $area);
        }
        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }
        if ($tier = $request->query('access_tier')) {
            $query->where('access_tier', $tier);
        }

        $exercises = $query->get();

        return response()->json([
            'data' => ExerciseResource::collection($exercises)->toArray($request),
        ]);
    }
}
