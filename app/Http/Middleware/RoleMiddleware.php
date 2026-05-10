<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Checks that the authenticated user has the required role.
     * Usage: route middleware `role:admin` or `role:vendor`.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login');
        }

        if (auth()->user()->role !== $role) {
            // If the expected role is admin, redirect to admin login.
            // For any other role mismatch, abort with 403.
            if ($role === 'admin') {
                return redirect()->route('admin.login');
            }

            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
