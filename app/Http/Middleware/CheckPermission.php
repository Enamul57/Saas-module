<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Check if user is super_admin
        $roles = $user->getRoleNames()->map(fn($role) => strtolower($role))->toArray();
        if (in_array('super_admin', $roles)) {
            return $next($request);
        }

        // ✅ DIRECT DATABASE QUERY - Works!
        $hasPermission = DB::table('role_has_permissions')
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('permissions.name', $permission)
            ->exists();

        // If not found, try direct user permissions
        if (!$hasPermission) {
            $hasPermission = DB::table('model_has_permissions')
                ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
                ->where('model_has_permissions.model_id', $user->id)
                ->where('model_has_permissions.model_type', 'App\\Models\\User')
                ->where('permissions.name', $permission)
                ->exists();
        }

        if ($hasPermission) {
            return $next($request);
        }

        // Deny access with proper error page
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You do not have permission to perform this action.',
                'permission' => $permission
            ], 403);
        }

        if (class_exists(Inertia::class)) {
            return Inertia::render('Errors/Forbidden', [
                'permission' => $permission,
                'message' => "You do not have permission to perform this action: {$permission}"
            ])->toResponse($request);
        }

        abort(403, "You do not have permission to perform this action: {$permission}");
    }
}