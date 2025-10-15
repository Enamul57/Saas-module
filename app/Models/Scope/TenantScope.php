<?php

namespace App\Models\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    //
    public function apply(Builder $builder, Model $model)
    {
        if ($tenantId = app('tenant')->id) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }
}
