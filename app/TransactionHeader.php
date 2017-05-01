<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use HelperService;

class TransactionHeader extends Model
{
    protected $guarded = ['id'];

    function totalTransaction($for_view=false)
    {
        $return = $this->grand_total_item_price+$this->others-$this->total_item_discount-$this->discount_total_fixed_value;

        return $for_view ? HelperService::maskMoney($return) : $return;
    }

    function firstPayment($for_view=false)
    {
        $return = $this->total_paid-$this->change;

        return $for_view ? HelperService::maskMoney($return) : $return;
    }

    function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    function rentingDatas()
    {
        return $this->hasMany('App\RentingData', 'transaction_id', 'id');
    }
}
