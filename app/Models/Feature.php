<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class Feature extends Model
{
    //
    protected $guarded = [];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'feature_role', 'feature_id', 'role_id')->withTimeStamps();
    }

    public function setNameAttribute($value)
    {
        $this->attribute['name'] = $value;
        $this->attribute['slug'] = Str::slug(strtolower($value));
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'feature_permission', 'feature_id', 'permission_id')->withTimeStamps();
    }
}
