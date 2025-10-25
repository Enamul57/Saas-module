<?php

namespace Modules\Setting\App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Setting\Database\Factories\SettingFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    // protected static function newFactory(): SettingFactory
    // {
    //     // return SettingFactory::new();
    // }

    public static function booted()
    {
        static::addGlobalScope(new TenantScope);
        static::creating(function ($model) {
            if ($tenant_id = app('tenant')->id) {
                $model->tenant_id = $tenant_id;
            }
        });
    }
}
