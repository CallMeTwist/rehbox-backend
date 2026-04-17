<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->latest()
            ->limit(30)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(Request $request, AppNotification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Marked as read.']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All marked as read.']);
    }
}
