<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $guarded = [];

    public function employeeAllowance()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }
}
