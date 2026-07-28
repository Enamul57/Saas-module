<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
            abort(403, 'Unauthorized.');
        }

        $user = Auth::user();

        // Super Admin has all roles
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have the required role.',
                    'required_roles' => $roles
                ], 403);
            }
            abort(403, "You do not have the required role. Required: " . implode(', ', $roles));
        }

        return $next($request);
    }
}
