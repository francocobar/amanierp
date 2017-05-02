<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalary extends Model
{
    protected $fillable = ['employee_id', 'user_id', 'employee_salary', 'valid_since', 'created_by'];

    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    function user()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
