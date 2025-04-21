<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'id');
    }
}