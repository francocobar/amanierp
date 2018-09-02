<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeIncentive extends Model
{
    protected $guarded = ['id'];

    function employee()
    {
        return $this->hasOne('App\Employee', 'employee_id', 'employee_id');
    }

    function setBy()
    {
        return $this->hasOne('App\User', 'id', 'set_by');
    }

    function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    function detail()
    {
        return $this->hasOne('App\TransactionDetail', 'id', 'detail_id');
    }
}
