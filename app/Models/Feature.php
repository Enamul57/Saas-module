<?php

namespace App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\TenantPermission as Permission;

class Feature extends Model
{
    //
    protected $guarded = [];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'feature_role', 'feature_id', 'role_id')->withTimeStamps()->withPivot('tenant_id')->wherePivot('tenant_id', app('tenant')->id);
    }

    public function setNameAttribute($value)
    {
        $this->attribute['name'] = $value;
        $this->attribute['slug'] = Str::slug(strtolower($value));
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'feature_permission',   // pivot table
            'feature_id',           // foreign key on pivot for this model
            'permission_id'         // foreign key on pivot for related model
        )
            ->withPivot('tenant_id')    // include tenant_id from pivot
            ->withTimestamps()
            ->wherePivot('tenant_id', app('tenant')->id);  // filter by tenant
    }
}
