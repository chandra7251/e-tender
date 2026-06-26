<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Mendukung multiple roles: role:admin  ATAU  role:admin,procurement_manager
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        $userRole = auth()->user()->role;

        if (!in_array($userRole, $roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['status' => false, 'message' => 'Akses ditolak. Role tidak sesuai.'], 403);
            }
            // Admin roles — redirect ke dashboard dengan flash error
            if (in_array($userRole, ['admin', 'super_admin', 'procurement_manager', 'evaluator', 'verifikator', 'auditor'])) {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
