<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Employee extends Model
{
    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $guarded = [];
    // protected $fillable = ['employee_id', 'full_name', 'dob', 'address', 'phone', 'branch_id', 'user_id', 'created_by'];

    public function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    function workSince($for='view')
    {
        if($this->work_since == null) {
            return 'Unset';
        }
        if($for == 'view') {
            return DateTime::createFromFormat('Y-m-d H:i:s', $this->work_since)->format('d/m/Y');
        }

        //for input format
        return $this->work_since;
    }
}
