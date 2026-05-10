<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lightweight Bearer-token authentication for the Vendor API.
 *
 * Strategy: token is stored in users.remember_token (already exists, no migration needed).
 * Generated on login via Str::random(60).
 *
 * SWAP NOTE: Replace this middleware with Sanctum's auth:sanctum or
 * Passport's auth:api when the final token strategy is confirmed.
 */
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

        // Guard: vendor profile must exist (not soft-deleted)
        if (!$user->vendor) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Profil vendor tidak ditemukan.',
            ], 403);
        }

        // Make user available via auth() helper throughout the request
        auth()->setUser($user);

        return $next($request);
    }
}
