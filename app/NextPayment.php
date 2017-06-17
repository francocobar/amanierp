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
}
