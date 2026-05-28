<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseLibraryController extends Controller
{
    // Browse all exercises — available to ALL PTs (vetted and unvetted)
    public function index(Request $request)
    {
        $query = Exercise::active();

        if ($request->filled('area')) {
            $query->area($request->area);
        }

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('access_tier')) {
            $query->where('access_tier', $request->query('access_tier'));
        }

        $exercises = $query->orderBy('area')->orderBy('category')->orderBy('title')->get();

        return ExerciseResource::collection($exercises);
    }

    public function show(Exercise $exercise)
    {
        return new ExerciseResource($exercise);
    }
}
