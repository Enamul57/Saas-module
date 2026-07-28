<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('tenant.login');
        }

        // 2. Check if user belongs to the current tenant
        $user = Auth::user();
        $tenantId = app('tenant')->id ?? session('tenant_id');

        // If user has tenant_id and it doesn't match current tenant
        if ($user->tenant_id && $user->tenant_id != $tenantId) {
            Auth::logout();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access to tenant.'], 403);
            }

            return redirect()->route('tenant.login')
                ->with('error', 'You do not have access to this tenant.');
        }

        return $next($request);
    }
}
