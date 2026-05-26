<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'             => \App\Http\Middleware\RoleMiddleware::class,
            'vendor.approved'  => \App\Http\Middleware\EnsureVendorApproved::class,
            // auth.api dihapus — diganti auth:api (JWT guard)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Semua unhandled exception di API route → return JSON, bukan HTML
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request): ?JsonResponse {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                // Jangan expose pesan internal di production
                $message = $status >= 500
                    ? 'Terjadi kesalahan server. Silakan coba beberapa saat lagi.'
                    : $e->getMessage();

                return response()->json([
                    'status'  => false,
                    'message' => $message,
                    'data'    => null,
                ], $status);
            }

            return null; // fallback ke handler default untuk non-API
        });
    })->create();

