<?php

namespace App\traits;

use Illuminate\Support\Facades\DB;

trait HasPermissionDirect
{
    public function hasPermissionDirect($permission): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Check super_admin
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Direct database query
        return DB::table('role_has_permissions')
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('permissions.name', $permission)
            ->exists();
    }
}