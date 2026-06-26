<?php
namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class ApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token tidak ditemukan. Silakan login.',
            ], 401);
        }
        $user = User::where('remember_token', $token)->first();
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token tidak valid atau sudah kedaluwarsa.',
            ], 401);
        }
        if ($user->role !== 'vendor') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses ditolak.',
            ], 403);
        }
        if (!$user->vendor) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Profil vendor tidak ditemukan.',
            ], 403);
        }
        auth()->setUser($user);
        return $next($request);
    }
}
