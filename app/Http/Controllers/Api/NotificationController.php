<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Fungsi buat nampilin daftar notifnya si user yang lagi login
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notifications = $user->notifications()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        // Di-convert jadi array dulu biar kita bisa nyelipin data 'unread_count' ke dalem responsenya
        $paginatedData = $notifications->toArray();
        $paginatedData['unread_count'] = $unreadCount;

        return response()->json([
            'status' => 'success',
            'data'   => $paginatedData,
        ]);
    }

    // Fungsi buat nandain satu notif doang kalo udah dibaca
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

    // Fungsi sapu jagat buat nandain semua notif udah dibaca
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

    // Fungsi buat ngehapus notifikasi
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->delete();
            return response()->json(['status' => 'success', 'message' => 'Notification deleted']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }

    // Fungsi buat hapus semua notifikasi
    public function destroyAll(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->notifications()->delete();

        return response()->json(['status' => 'success', 'message' => 'All notifications deleted']);
    }
}
