<?php

namespace App\Http\Controllers\Api\PT;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;


class ExerciseLibraryController extends Controller
{
    // Browse all exercises — available to ALL PTs (vetted and unvetted)
    public function index(Request $request)
    {
        $query = Exercise::active();

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $exercises = $query->paginate(20);

        return response()->json($exercises);
    }

    public function show(Exercise $exercise)
    {
        return response()->json($exercise);
    }
}
