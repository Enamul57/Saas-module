<?php

namespace Modules\PIM\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PIM\Database\Factories\EmployeeStatusFactory;

class EmployeeStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): EmployeeStatusFactory
    // {
    //     // return EmployeeStatusFactory::new();
    // }
}
