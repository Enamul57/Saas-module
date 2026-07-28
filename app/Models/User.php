<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Scope\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\PIM\App\Models\Employee as ModelsEmployee;
use Modules\PIM\Models\Employee;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRoleAttribute($value)
    {
        return $value ? strtolower($value) : null;
    }

    public function employee()
    {
        return $this->hasOne(ModelsEmployee::class);
    }
    public static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }
    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = $value ? strtolower($value) : null;
    }
}
