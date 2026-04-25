<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreAssessmentRequest;
use App\Models\ClientAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function store(StoreAssessmentRequest $request): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');

        $assessment = DB::transaction(function () use ($request, $client) {
            $assessment = ClientAssessment::updateOrCreate(
                ['client_id' => $client->id],
                $request->validated()
            );

            $client->update(['assessment_completed_at' => now()]);

            return $assessment;
        });

        return response()->json(['data' => $assessment], 201);
    }

    public function show(Request $request): JsonResponse
    {
        $client = $request->user()->client;
        abort_if($client === null, 403, 'Client profile missing.');

        $assessment = $client->assessment;
        abort_if($assessment === null, 404, 'No assessment yet.');

        return response()->json(['data' => $assessment]);
    }
}
