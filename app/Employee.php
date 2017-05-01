<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['employee_id', 'full_name', 'dob', 'address', 'phone', 'branch_id', 'user_id', 'created_by'];

    public function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }
}
