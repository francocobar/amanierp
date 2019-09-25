<?php

namespace App\Http\Controllers\Promo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Helper\CouponHelper;
class CouponController extends Controller
{
    protected $error_message = '';
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('superadmin')->only('couponManagement', 'createCoupon');
    }


    protected function couponManagement()
    {
        $data['title'] = 'Manajemen Kupon';
        $data['headline'] = 'Manajemen Kupon';
        return view('promo.coupon-management', $data);
    }

    protected function createCoupon()
    {
        // return array('data'=>false, 'message' => '');
        $inputs = request()->all();
        $new_coupon = [];
        if(isset($inputs['is_percent']) && $inputs['is_percent'] == '1') {
            $new_coupon['disc_value'] = $inputs['disc_percent_value'];
            $new_coupon['disc_value_type'] = 1;
            if(isset($inputs['has_max_fix_value']) && $inputs['has_max_fix_value'] == '1') {
                $new_coupon['max_fix_value'] = $inputs['disc_max_fix_value'];
            }
        }
        else {
            $new_coupon['disc_value'] = $inputs['disc_fix_value'];
            $new_coupon['disc_value_type'] = 2;
        }
        $new_coupon['coupon_name'] = $inputs['promo_name'];
        $new_coupon['coupon_code'] = $inputs['promo_code'];
        $new_coupon['valid_from'] = $inputs['promo_valid_from'];
        $new_coupon['valid_to'] = $inputs['promo_valid_to'];
        $creating_coupon = CouponHelper::createNewCoupon($new_coupon);
        return $creating_coupon;
        if(CouponHelper::createNewCoupon($new_coupon)) {
            return array('data'=>true);
        }
    }

    function getAllCoupons()
    {
        $promos = CouponHelper::getAllCoupons();

        $return = '';
        foreach ($promos as $key => $promo) {
            $return .= '<tr><td>'.$promo->coupon_name.'</td><td>'.$promo->coupon_code.'</td><td>';
            $disc_value = '';
            $max_value = '';
            if($promo->disc_value_type == 1) {
                $disc_value = $promo->discValueStr(true);
                $max_value = $promo->maxValueStr();
            }
            else {
                $disc_value = $promo->discValueStr(false);
            }
            $return .=  $disc_value.'</td><td>'.$max_value.'</td><td>sejak</td><td>hingga</td><td>desk</td>';

        }

        return $return;
    }
}
