<?php

namespace Modules\PIM\App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PIM\Database\Factories\EmployeeFactory;
use App\Models\User;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    // protected static function newFactory(): EmployeeFactory
    // {
    //     // return EmployeeFactory::new();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function booted()
    {
        static::addGlobalScope(TenantScope::class);
        static::creating(function ($model) {
            if ($tenant_id = app('tenant')->id) {
                $model->tenant_id = $tenant_id;
            }
        });
    }
}
