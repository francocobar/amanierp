<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use HelperService;

class NextPayment extends Model
{
    protected $guarded = ['id'];

    function paidValue($for_view=false)
    {
        return $for_view ? HelperService::maskMoney($this->paid_value) : $this->paid_value;
    }

    function header()
    {
        return $this->hasOne('App\TransactionHeader', 'id', 'header_id');
    }

    function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }
}
