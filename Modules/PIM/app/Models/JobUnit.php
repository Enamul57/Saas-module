<?php

namespace Modules\PIM\App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PIM\Database\Factories\JobUnitFactory;

class JobUnit extends Model
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
    public function category()
    {
        return $this->belongsTo(JobCategory::class);
    }
    public function titles()
    {
        return $this->hasMany(JobTitle::class);
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
    // protected static function newFactory(): JobUnitFactory
    // {
    //     // return JobUnitFactory::new();
    // }
}
