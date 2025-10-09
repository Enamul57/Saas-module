<?php

namespace App\traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

trait BelongsToTenant
{
    // Apply a global scope for tenant filtering
    public static function bootBelongsToTenant()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Config::has('tenant_id')) {
                $builder->where('tenant_id', Config::get('tenant_id'));
            }
        });

        // Automatically set tenant_id when creating a new record
        static::creating(function ($model) {
            if (Config::has('tenant_id')) {
                $model->tenant_id = Config::get('tenant_id');
            }
        });
    }
}
