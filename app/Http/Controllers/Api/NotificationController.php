<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function resolveUser(Request $request): ?User
    {
        return auth('api')->user() ?? $request->user('api') ?? $request->user();
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return $this->error('Unauthenticated.', null, 401);
        }
        $notifications = $user->notifications()->paginate(15);
        $data = $notifications->toArray();
        $data['unread_count'] = $user->unreadNotifications()->count();
        return $this->success($data);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return $this->error('Unauthenticated.', null, 401);
        }
        $notification = $user->notifications()->find($id);
        if (!$notification) {
            return $this->error('Notifikasi tidak ditemukan.', null, 404);
        }
        $notification->markAsRead();
        return $this->success(null, 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return $this->error('Unauthenticated.', null, 401);
        }
        $user->unreadNotifications->markAsRead();
        return $this->success(null, 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return $this->error('Unauthenticated.', null, 401);
        }
        $notification = $user->notifications()->find($id);
        if (!$notification) {
            return $this->error('Notifikasi tidak ditemukan.', null, 404);
        }
        $notification->delete();
        return $this->success(null, 'Notifikasi dihapus.');
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return $this->error('Unauthenticated.', null, 401);
        }
        $user->notifications()->delete();
        return $this->success(null, 'Semua notifikasi dihapus.');
    }
}