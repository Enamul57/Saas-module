<?php

namespace App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Exceptions\RoleAlreadyExists;

class TenantRole extends Role
{
    //
    protected $fillable = ['name', 'guard_name', 'tenant_id'];

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        $query = static::where('name', $attributes['name'])->where('guard_name', $attributes['guard_name']);

        if (array_key_exists('tenant_id', $attributes) && $attributes['tenant_id']) {
            $query->where('tenant_id', $attributes['tenant_id']);
        } else {
            $query->whereNull('tenant_id');
        }
        $existingRole = $query->first();

        if ($existingRole) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }
        return static::query()->create($attributes);
    }
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_role', 'role_id', 'feature_id');
    }
    public static function booted()
    {
        // Apply global scope for tenant isolation
        static::addGlobalScope(new TenantScope);

        // Automatically set tenant_id when creating
        static::creating(function ($model) {
            if (!$model->tenant_id) {
                $model->tenant_id = app('tenant')->id ?? session('tenant_id');
            }
        });

        // Prevent updating across tenants (optional but recommended)
        static::updating(function ($model) {
            if ($model->tenant_id !== (app('tenant')->id ?? session('tenant_id'))) {
                throw new \Exception("Unauthorized update across tenants.");
            }
        });

        // Prevent deleting across tenants (optional but recommended)
        static::deleting(function ($model) {
            if ($model->tenant_id !== (app('tenant')->id ?? session('tenant_id'))) {
                throw new \Exception("Unauthorized delete across tenants.");
            }
        });
    }
}
