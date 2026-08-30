<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\PIM\App\Models\Employee as ModelsEmployee;
use Modules\PIM\Models\Employee;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRoleAttribute($value)
    {
        return $value ? strtolower($value) : null;
    }

    public function employee()
    {
        return $this->hasOne(ModelsEmployee::class);
    }

    public static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = $value ? strtolower($value) : null;
    }

    /**
     * Get all features assigned to the user through their roles
     * 
     * @return array
     */
    public function getAllFeatures()
    {
        // Get all roles with their features
        $roles = $this->roles()->with('features')->get();

        // Collect all features from all roles and make them unique
        $features = $roles->flatMap(function ($role) {
            return $role->features;
        })->unique('id');

        // Return just the feature names (using the 'name' attribute from Feature model)
        return $features->pluck('name')->toArray();
    }

    /**
     * Get all permissions assigned to the user through their roles
     * Also includes permissions directly assigned to the user if any
     * 
     * @return array
     */
    public function getAllPermissionsFromRoles()
    {
        // Get all roles with their permissions
        $roles = $this->roles()->with('permissions')->get();

        // Collect all permissions from all roles and make them unique
        $permissions = $roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id');

        // Also get any permissions directly assigned to the user (if using Spatie's direct assignment)
        $directPermissions = $this->permissions()->get();
        $allPermissions = $permissions->merge($directPermissions)->unique('id');

        // Return just the permission names
        return $allPermissions->pluck('name')->toArray();
    }

    /**
     * Get complete user data for authentication with roles, permissions, and features
     * 
     * @return array
     */
    public function getAuthData()
    {
        $employee = $this->employee;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'tenant_id' => $this->tenant_id,
            'role' => $this->role,
            'roles' => $this->getRoleNames()->toArray(),
            'permissions' => $this->getAllPermissionsFromRoles(),
            'features' => $this->getAllFeatures(),
            'employee_id' => $employee ? $employee->id : null,
        ];
    }

    /**
     * Check if user has a specific feature/module
     * 
     * @param string $featureName
     * @return bool
     */
    public function hasFeature($featureName)
    {
        // Super admin bypass - check if user has super_admin role
        if ($this->hasRole('super_admin')) {
            return true;
        }

        $features = $this->getAllFeatures();
        return in_array($featureName, $features);
    }

    /**
     * Check if user has any of the given features/modules
     * 
     * @param array $featureNames
     * @return bool
     */
    public function hasAnyFeature($featureNames)
    {
        // Super admin bypass
        if ($this->hasRole('super_admin')) {
            return true;
        }

        $features = $this->getAllFeatures();
        foreach ($featureNames as $featureName) {
            if (in_array($featureName, $features)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given features/modules
     * 
     * @param array $featureNames
     * @return bool
     */
    public function hasAllFeatures($featureNames)
    {
        // Super admin bypass
        if ($this->hasRole('super_admin')) {
            return true;
        }

        $features = $this->getAllFeatures();
        foreach ($featureNames as $featureName) {
            if (!in_array($featureName, $features)) {
                return false;
            }
        }
        return true;
    }
}