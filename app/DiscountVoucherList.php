<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountVoucherList extends Model
{
    protected $table ='discount_voucher_list';

    protected $guarded = ['id'];

    function voucherHeader()
    {
        return $this->hasOne('App\DiscountVoucher', 'id', 'voucher_id');
    }
}
