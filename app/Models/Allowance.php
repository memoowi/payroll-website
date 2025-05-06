<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function employeeAllowance()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }
}
