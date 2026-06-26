<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(15);
        $data = $notifications->toArray();
        $data['unread_count'] = $user->unreadNotifications()->count();
        return $this->success($data);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);
        if (!$notification) {
            return $this->error('Notifikasi tidak ditemukan.', null, 404);
        }
        $notification->markAsRead();
        return $this->success(null, 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->success(null, 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);
        if (!$notification) {
            return $this->error('Notifikasi tidak ditemukan.', null, 404);
        }
        $notification->delete();
        return $this->success(null, 'Notifikasi dihapus.');
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $request->user()->notifications()->delete();
        return $this->success(null, 'Semua notifikasi dihapus.');
    }
}
