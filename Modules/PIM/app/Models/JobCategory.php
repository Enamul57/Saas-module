<?php

namespace Modules\PIM\App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PIM\Database\Factories\JobCategoryFactory;

class JobCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    public function jobDetails()
    {
        return $this->hasMany(JobDetails::class);
    }

    public function units()
    {
        return $this->haMany(JobUnit::class);
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
    // protected static function newFactory(): JobCategoryFactory
    // {
    //     // return JobCategoryFactory::new();
    // }
}
