<?php

namespace App\Model\Promo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    function maxValueStr()
    {
        return 'Rp '.number_format($this->max_fix_value, 0, ',', '.');
    }

    function discValueStr($is_percent)
    {
        if($is_percent) {
            return number_format($this->disc_value, 0, ',', '.').'%';
        }
        return 'Rp '.number_format($this->disc_value, 0, ',', '.');
    }
}
