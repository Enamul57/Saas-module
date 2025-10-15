<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Guard;

class TenantPermission extends SpatiePermission
{
    protected $guarded = [];

    /**
     * Tenant-aware create method
     */
    public static function create(array $attributes = [])
    {
        $tenantId = $attributes['tenant_id'] ?? (app('tenant')->id ?? session('tenant_id'));
        $attributes['tenant_id'] = $tenantId;
        $attributes['guard_name'] ??= Guard::getDefaultName(static::class);

        // Check uniqueness for this tenant
        $permission = static::where('slug', $attributes['slug'])
            ->where('guard_name', $attributes['guard_name'])
            ->where('tenant_id', $tenantId)
            ->first();

        if ($permission) {
            // Already exists for this tenant, just return it instead of throwing
            return $permission;
        }

        return static::query()->create($attributes);
    }

    public static function booted()
    {
        parent::booted();

        // Automatically assign tenant_id if not set
        static::creating(function ($model) {
            if (!$model->tenant_id) {
                $model->tenant_id = app('tenant')->id ?? session('tenant_id');
            }
        });
    }
}
