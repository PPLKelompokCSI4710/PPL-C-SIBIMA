<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetch the latest notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => data_get($n->data, 'type', 'general'),
                'title' => data_get($n->data, 'title', 'Notifikasi'),
                'message' => data_get($n->data, 'message', ''),
                'detail' => data_get($n->data, 'detail', []),
                'read_at' => $n->read_at,
                'created_at' => $n->created_at->diffForHumans(),
                'created_at_iso' => $n->created_at->toIso8601String(),
            ]);

        $unreadCount = $request->user()->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
