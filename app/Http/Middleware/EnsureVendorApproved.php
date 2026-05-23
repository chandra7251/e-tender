<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorApproved
{
    use ApiResponse;

    /**
     * Tolak request jika vendor belum diverifikasi admin.
     * Gunakan pada route yang memerlukan vendor berstatus 'approved'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        // Pastikan user adalah vendor dan sudah login
        if (!$user || !$user->vendor) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun vendor tidak ditemukan.',
            ], 403);
        }

        if ($user->vendor->verification_status !== 'approved') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda belum diverifikasi. Silakan tunggu persetujuan dari admin.',
                'data'    => [
                    'verification_status' => $user->vendor->verification_status,
                ],
            ], 403);
        }

        return $next($request);
    }
}
