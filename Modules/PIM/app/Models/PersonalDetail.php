<?php

namespace Modules\PIM\App\Models;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\PIM\App\Models\Employee;

// use Modules\PIM\Database\Factories\PersonalDetailFactory;

class PersonalDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected $casts = [
        'dob' => 'date',
    ];
    protected $appends = [
        'formatted_dob'
    ];

    public function formattedDob(): Attribute
    {
        return Attribute::get(
            fn() => $this->dob?->format('Y-m-d')
        );
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
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
    // protected static function newFactory(): PersonalDetailFactory
    // {
    //     // return PersonalDetailFactory::new();
    // }
}
