<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has the permission or is super_admin
        $roles = $user->getRoleNames()->map(fn($role) => strtolower($role))->toArray();

        if ($user->can($permission) || in_array('super_admin', $roles)) {
            return $next($request);
        }

        // Deny access with proper error page
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You do not have permission to perform this action.',
                'permission' => $permission
            ], 403);
        }

        // Check if Inertia is being used
        if (class_exists(Inertia::class)) {
            return Inertia::render('Errors/Forbidden', [
                'permission' => $permission,
                'message' => "You do not have permission to perform this action: {$permission}"
            ])->toResponse($request);
        }

        // Fallback to Blade error page
        abort(403, "You do not have permission to perform this action: {$permission}");
    }
}
