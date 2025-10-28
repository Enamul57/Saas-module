<?php

namespace Modules\PIM\App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PIM\Database\Factories\AttachmentFactory;

class Attachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    public function personal_details()
    {
        return $this->morphTo(PersonalDetail::class, 'attachable');
    }
    public static function booted()
    {
        static::addGlobalScope(new TenantScope);
        static::creating(function ($model) {
            if ($tenant_id = app('tenant')->id) {
                $model->tenant_id = $tenant_id;
            }
        });
    }
    // protected static function newFactory(): AttachmentFactory
    // {
    //     // return AttachmentFactory::new();
    // }
}
