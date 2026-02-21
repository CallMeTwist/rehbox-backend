<?php

use App\Http\Controllers\Api\Auth\ClientAuthController;
use App\Http\Controllers\Api\Auth\PTAuthController;
use App\Http\Controllers\Api\Client\PlanController;
use App\Http\Controllers\Api\Client\SubscriptionController;
use App\Http\Controllers\Api\PT\ClientController;
use App\Http\Controllers\Api\PT\ExerciseLibraryController;
use App\Http\Controllers\Api\PT\ExercisePlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/pt/register',     [PTAuthController::class, 'register']);
    Route::post('/pt/login',        [PTAuthController::class, 'login']);
    Route::post('/client/register', [ClientAuthController::class, 'register']);
    Route::post('/client/login',    [ClientAuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [ClientAuthController::class, 'logout']);

    // Shared: get current user profile
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load(['physiotherapist', 'client']);
        return response()->json(['user' => $user]);
    });

    // PT routes — any authenticated PT (vetted + unvetted)
    Route::prefix('pt')->middleware('role:pt')->group(function () {
        // These are available to unvetted PTs:
        // (exercise library will be a separate route group in Phase 2)
        Route::get('/exercises',       [ExerciseLibraryController::class, 'index']);
        Route::get('/exercises/{exercise}', [ExerciseLibraryController::class, 'show']);

        // These require vetting:
        Route::middleware('vetted')->group(function () {
            // Phase 2 routes go here (clients, plans, etc.)
            Route::get('/clients',           [ClientController::class, 'index']);
            Route::get('/clients/{clientId}', [ClientController::class, 'show']);
            Route::post('/plans',            [ExercisePlanController::class, 'store']);
            Route::put('/plans/{plan}',      [ExercisePlanController::class, 'update']);
            Route::delete('/plans/{plan}',   [ExercisePlanController::class, 'destroy']);
        });
    });

    // Client routes
    Route::prefix('client')->middleware('role:client')->group(function () {
        // Phase 2+ routes here
        Route::get('/plan',         [PlanController::class, 'myPlan']);
        Route::post('/subscribe',   [SubscriptionController::class, 'initialize']);
    });
});
