<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notifications = $user->notifications()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        // Convert the paginated response to an array so we can append unread_count to the 'data' payload
        $paginatedData = $notifications->toArray();
        $paginatedData['unread_count'] = $unreadCount;

        return response()->json([
            'status' => 'success',
            'data'   => $paginatedData,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success', 'message' => 'Notification marked as read']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }
}
