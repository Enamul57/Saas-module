<?php

namespace App\Models;

use App\Models\Scope\TenantScope;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Support\Str;

class Tenant extends BaseTenant
{
    use HasDomains;
    protected $guarded = [];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company_name',
            'email',
            'slug',
            'password',
        ];
    }

    public function setPasswordAttribute(string $password): void
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function setCompanyNameAttribute(string $company_name): void
    {
        $this->attributes['company_name'] = $company_name;
        $this->attributes['slug'] = Str::slug($company_name);
    }

    public static function booted()
    {
        static::addGlobalScope(new TenantScope);
        static::creating(function ($model) {
            if ($tenant_id = session('tenant_id')) {
                $model->tenant_id = $tenant_id;
            }
        });
    }
}
