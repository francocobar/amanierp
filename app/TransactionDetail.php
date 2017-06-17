<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $guarded = ['id'];

    function itemInfo()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id');
    }

    function header()
    {
        return $this->hasOne('App\TransactionHeader', 'id', 'header_id');
    }

    function itemTurnover()
    {
        return $this->item_total_price-$this->item_discount_fixed_value;
    }

    function employeeIncentives()
    {
        return $this->hasMany('App\EmployeeIncentive', 'detail_id', 'id');
    }
}
