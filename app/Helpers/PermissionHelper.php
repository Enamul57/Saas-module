<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    public static function can($permission)
    {
        $user = Auth::user();
        if (!$user) return false;

        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->can($permission);
    }

    public static function hasRole($role)
    {
        $user = Auth::user();
        if (!$user) return false;

        return $user->hasRole($role);
    }

    public static function hasAnyRole($roles)
    {
        $user = Auth::user();
        if (!$user) return false;

        return $user->hasAnyRole($roles);
    }
}
