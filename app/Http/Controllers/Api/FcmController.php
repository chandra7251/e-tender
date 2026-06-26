<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmController extends BaseApiController
{
    /**
     * Simpan/update FCM token milik user yang login.
     * Mobile app harus memanggil endpoint ini saat:
     *   - User login pertama kali
     *   - Token di-refresh oleh Firebase SDK
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string|max:512',
        ]);

        $user = auth('api')->user();
        $user->update(['fcm_token' => $request->input('fcm_token')]);

        return $this->success(null, 'FCM token berhasil disimpan.');
    }

    /**
     * Hapus FCM token saat user logout dari device ini.
     */
    public function unregister(): JsonResponse
    {
        auth('api')->user()->update(['fcm_token' => null]);

        return $this->success(null, 'FCM token berhasil dihapus.');
    }
}
