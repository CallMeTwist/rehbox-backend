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
use App\Http\Controllers\Api\ChatFileController;
use App\Http\Controllers\Api\Client\AssessmentController;
use App\Http\Controllers\Api\Client\ChatController;
use App\Http\Controllers\Api\Client\ExerciseCompletionController;
use App\Http\Controllers\Api\Client\ExerciseLibraryController as ClientExerciseLibraryController;
use App\Http\Controllers\Api\Client\PlanController;
// ← add
use App\Http\Controllers\Api\Client\ProfileController;
use App\Http\Controllers\Api\Client\ProgressController;
use App\Http\Controllers\Api\Client\PushController;
use App\Http\Controllers\Api\Client\ReminderController;
use App\Http\Controllers\Api\Client\RewardController;
use App\Http\Controllers\Api\Client\SelfPlanController;
// ← add
use App\Http\Controllers\Api\Client\SessionController;
use App\Http\Controllers\Api\Client\ShopController;
use App\Http\Controllers\Api\Client\SubscriptionController;
use App\Http\Controllers\Api\PT\ClientController;
use App\Http\Controllers\Api\PT\DashboardController;
use App\Http\Controllers\Api\PT\EarningsController;
use App\Http\Controllers\Api\PT\ExerciseLibraryController;
use App\Http\Controllers\Api\PT\ExercisePlanController;
// ← add
use App\Http\Controllers\Api\PT\MotionReportController;
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
        $client = $user->client;

        return response()->json([
            'user' => $user,
            'subscription_plan' => $client?->subscription_plan ?? null,
            'assessment_completed_at' => $client?->assessment_completed_at ?? null,
        ]);
    });

    // Chat file download (shared — both PT and client)
    Route::get('/chat/files/{filename}', [ChatFileController::class, 'show'])
        ->where('filename', '.+');

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
        Route::post('/profile/avatar', [PTProfileController::class, 'uploadAvatar']);

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
            Route::post('/chat', [ChatController::class, 'store'])->middleware('throttle:60,1');
            Route::get('/chat/unread', [ChatController::class, 'unread']);
            Route::post('/chat/read', [ChatController::class, 'markRead'])->middleware('throttle:120,1');
            // Phase 4
            Route::get('/earnings', [EarningsController::class, 'index']);
            Route::get('/clients/{clientId}/motion-reports', [MotionReportController::class, 'clientReports']);
            Route::get('/sessions/{sessionId}/detail', [MotionReportController::class, 'sessionDetail']);

        });
    });

    // ── Client routes ─────────────────────────────────────────────────
    Route::prefix('client')->middleware('role:client')->group(function () {

        Route::post('/assessment', [AssessmentController::class, 'store']);
        Route::get('/assessment', [AssessmentController::class, 'show']);

        Route::get('/plan', [PlanController::class, 'myPlan']);
        Route::get('/exercises', [ClientExerciseLibraryController::class, 'index']);
        Route::post('/exercises/{exercise}/log-completion', [ExerciseCompletionController::class, 'store']);
        Route::post('/subscribe', [SubscriptionController::class, 'initialize']);

        Route::post('/push/subscribe', [PushController::class, 'subscribe']);
        Route::delete('/push/unsubscribe', [PushController::class, 'unsubscribe']);

        Route::get('/reminders', [ReminderController::class, 'index']);
        Route::post('/reminders', [ReminderController::class, 'store']);
        Route::put('/reminders/{reminder}', [ReminderController::class, 'update']);
        Route::patch('/reminders/{reminder}/toggle', [ReminderController::class, 'toggle']);
        Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy']);

        // ← Phase 3: profile + language
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::patch('/profile/language', [ProfileController::class, 'updateLanguage']);
        Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);

        Route::get('/progress', [ProgressController::class, 'index']);
        Route::get('/progress/report/{month}/{year}', [ProgressController::class, 'monthlyReport']);
        Route::get('/rewards', [RewardController::class, 'index']);

        // ← Phase 3: subscription required
        Route::middleware('subscribed')->group(function () {
            Route::post('/connect-pt', [ProfileController::class, 'connectPT']);

            Route::get('/chat', [ChatController::class, 'index']);
            Route::post('/chat', [ChatController::class, 'store'])->middleware('throttle:60,1');
            Route::get('/chat/unread', [ChatController::class, 'unread']);
            Route::post('/chat/read', [ChatController::class, 'markRead'])->middleware('throttle:120,1');

            Route::get('/shop', [ShopController::class, 'index']);
            Route::get('/shop/orders', [ShopController::class, 'myOrders']);

            Route::post('/plans/self', [SelfPlanController::class, 'store']);
            Route::put('/plans/self/{plan}', [SelfPlanController::class, 'update']);
            Route::delete('/plans/self/{plan}', [SelfPlanController::class, 'destroy']);

            Route::post('/sessions', [SessionController::class, 'start']);
            Route::put('/sessions/{session}/complete', [SessionController::class, 'complete']);
            Route::get('/sessions/history', [SessionController::class, 'history']);
            Route::post('/shop/{item}/purchase', [ShopController::class, 'purchase']);
        });

    });
});
