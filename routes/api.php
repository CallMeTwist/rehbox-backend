<?php

//
// use App\Http\Controllers\Api\Auth\ClientAuthController;
// use App\Http\Controllers\Api\Auth\PTAuthController;
// use App\Http\Controllers\Api\Client\PlanController;
// use App\Http\Controllers\Api\Client\PTProfileController;
// use App\Http\Controllers\Api\Client\SubscriptionController;
// use App\Http\Controllers\Api\PT\ClientController;
// use App\Http\Controllers\Api\PT\ExerciseLibraryController;
// use App\Http\Controllers\Api\PT\ExercisePlanController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
//
// Route::get('/user', function (Request $request) {
//    return $request->user();
// })->middleware('auth:sanctum');
//
// // Public auth routes
// Route::prefix('auth')->group(function () {
//    Route::post('/pt/register',     [PTAuthController::class, 'register']);
//    Route::post('/pt/login',        [PTAuthController::class, 'login']);
//    Route::post('/client/register', [ClientAuthController::class, 'register']);
//    Route::post('/client/login',    [ClientAuthController::class, 'login']);
// });
//
// // Authenticated routes
// Route::middleware('auth:sanctum')->group(function () {
//
//    Route::post('/auth/logout', [ClientAuthController::class, 'logout']);
//
//    // Shared: get current user profile
//    Route::get('/me', function (Request $request) {
//        $user = $request->user()->load(['physiotherapist', 'client']);
//        return response()->json(['user' => $user]);
//    });
//
//    // PT routes — any authenticated PT (vetted + unvetted)
//    Route::prefix('pt')->middleware('role:pt')->group(function () {
//        // These are available to unvetted PTs:
//        // (exercise library will be a separate route group in Phase 2)
//        Route::get('/exercises',       [ExerciseLibraryController::class, 'index']);
//        Route::get('/exercises/{exercise}', [ExerciseLibraryController::class, 'show']);
//
//        // These require vetting:
//        Route::middleware('vetted')->group(function () {
//            // Phase 2 routes go here (clients, plans, etc.)
//            Route::get('/clients',           [ClientController::class, 'index']);
//            Route::get('/clients/{clientId}', [ClientController::class, 'show']);
//            Route::post('/plans',            [ExercisePlanController::class, 'store']);
//            Route::put('/plans/{plan}',      [ExercisePlanController::class, 'update']);
//            Route::delete('/plans/{plan}',   [ExercisePlanController::class, 'destroy']);
//        });
//    });
//
//    // Client routes
//    Route::prefix('client')->middleware('role:client')->group(function () {
//        // Phase 2+ routes here
//        Route::get('/plan',         [PlanController::class, 'myPlan']);
//        Route::post('/subscribe',   [SubscriptionController::class, 'initialize']);
//        Route::get('/profile',            [PTProfileController::class, 'show']);
//        Route::patch('/profile/language', [PTProfileController::class, 'updateLanguage']);
//    });
// });

use App\Http\Controllers\Api\Auth\ClientAuthController;
use App\Http\Controllers\Api\Auth\PTAuthController;
use App\Http\Controllers\Api\Client\ChatController;
use App\Http\Controllers\Api\Client\PlanController;
use App\Http\Controllers\Api\Client\ProfileController;
// ← add
use App\Http\Controllers\Api\Client\ProgressController;
use App\Http\Controllers\Api\Client\PushController;
use App\Http\Controllers\Api\Client\RewardController;
use App\Http\Controllers\Api\Client\SessionController;
// ← add
use App\Http\Controllers\Api\Client\ShopController;
use App\Http\Controllers\Api\Client\SubscriptionController;
use App\Http\Controllers\Api\PT\ClientController;
use App\Http\Controllers\Api\PT\DashboardController;
use App\Http\Controllers\Api\PT\EarningsController;
use App\Http\Controllers\Api\PT\ExerciseLibraryController;
use App\Http\Controllers\Api\PT\ExercisePlanController;
use App\Http\Controllers\Api\PT\MotionReportController;
// ← add
use App\Http\Controllers\Api\PT\PTProfileController;
use App\Http\Controllers\Api\Shared\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/broadcasting/auth', function (Request $request) {
    // Force Sanctum guard instead of default web guard
    $request->headers->set('Accept', 'application/json');

    return Broadcast::auth($request);
})->middleware('auth:sanctum');

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/pt/register', [PTAuthController::class, 'register']);
    Route::post('/pt/login', [PTAuthController::class, 'login']);
    Route::post('/client/register', [ClientAuthController::class, 'register']);
    Route::post('/client/login', [ClientAuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [ClientAuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        $user = $request->user()->load(['physiotherapist', 'client']);

        return response()->json(['user' => $user]);
    });

    // Notifications (shared — both PT and client)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // ── PT routes ────────────────────────────────────────────────────
    Route::prefix('pt')->middleware('role:pt')->group(function () {

        Route::get('/exercises', [ExerciseLibraryController::class, 'index']);
        Route::get('/exercises/{exercise}', [ExerciseLibraryController::class, 'show']);
        Route::get('/dashboard', [DashboardController::class, 'stats']);

        Route::get('/profile', [PTProfileController::class, 'show']);
        Route::patch('/profile', [PTProfileController::class, 'update']);

        Route::middleware('vetted')->group(function () {
            Route::get('/clients', [ClientController::class, 'index']);
            Route::get('/clients/{clientId}', [ClientController::class, 'show']);
            Route::get('/plans/{plan}', [ExercisePlanController::class, 'show']);
            Route::post('/plans', [ExercisePlanController::class, 'store']);
            Route::put('/plans/{plan}', [ExercisePlanController::class, 'update']);
            Route::delete('/plans/{plan}', [ExercisePlanController::class, 'destroy']);
            Route::patch('/clients/{clientId}/condition', [ClientController::class, 'updateCondition']);
            // ← Phase 3: PT chat (vetted only)
            Route::get('/chat', [ChatController::class, 'index']);
            Route::post('/chat', [ChatController::class, 'store']);
            // Phase 4
            Route::get('/earnings', [EarningsController::class, 'index']);
            Route::get('/clients/{clientId}/motion-reports', [MotionReportController::class, 'clientReports']);
            Route::get('/sessions/{sessionId}/detail', [MotionReportController::class, 'sessionDetail']);

        });
    });

    // ── Client routes ─────────────────────────────────────────────────
    Route::prefix('client')->middleware('role:client')->group(function () {

        Route::get('/plan', [PlanController::class, 'myPlan']);
        Route::post('/subscribe', [SubscriptionController::class, 'initialize']);

        Route::post('/push/subscribe', [PushController::class, 'subscribe']);
        Route::delete('/push/unsubscribe', [PushController::class, 'unsubscribe']);

        // ← Phase 3: profile + language
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::patch('/profile/language', [ProfileController::class, 'updateLanguage']);
        // routes/api.php — inside client group, no subscription required
        Route::post('/connect-pt', [ProfileController::class, 'connectPT']);

        // ← Phase 3: chat (no subscription required)
        Route::get('/chat', [ChatController::class, 'index']);
        Route::post('/chat', [ChatController::class, 'store']);
        Route::get('/progress', [ProgressController::class, 'index']);
        Route::get('/progress/report/{month}/{year}', [ProgressController::class, 'monthlyReport']);
        Route::get('/rewards', [RewardController::class, 'index']);
        Route::get('/shop', [ShopController::class, 'index']);
        Route::get('/shop/orders', [ShopController::class, 'myOrders']);

        // ← Phase 3: sessions (subscription required)
        Route::middleware('subscribed')->group(function () {
            Route::post('/sessions', [SessionController::class, 'start']);
            Route::put('/sessions/{session}/complete', [SessionController::class, 'complete']);
            Route::get('/sessions/history', [SessionController::class, 'history']);
            Route::post('/shop/{item}/purchase', [ShopController::class, 'purchase']);
        });

    });
});
