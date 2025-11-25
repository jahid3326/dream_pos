<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Mark all unread notifications as read
            $user->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification count for debugging
     */
    public function getCount()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $unreadCount = $user->unreadNotifications()->count();
            $totalCount = $user->notifications()->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'total_count' => $totalCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting notification count: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest notifications for real-time updates
     */
    public function getLatest()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $notifications = $user->unreadNotifications()->latest()->take(10)->get();
            $totalUnreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'data' => $notification->data,
                        'created_at' => $notification->created_at->diffForHumans(),
                        'created_at_iso' => $notification->created_at->toISOString()
                    ];
                }),
                'unread_count' => $totalUnreadCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read: ' . $e->getMessage()
            ], 500);
        }
    }
}
