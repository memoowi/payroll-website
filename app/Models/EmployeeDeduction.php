<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    protected $guarded = [];

    public function deduction()
    {
        return $this->belongsTo(Deduction::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}