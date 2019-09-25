<?php

namespace App\Helper;
use App\Model\Promo\Coupon;
use Illuminate\Support\Facades\Cache;


class CouponHelper
{
    public function __construct()
    {

    }

    static function createNewCoupon($coupon_data)
    {
        $coupons = CouponHelper::getCouponsByCode($coupon_data['coupon_code']);
        // dd($coupon);
        if($coupons) {
            foreach($coupons as $key => $coupon) {
                if($coupon_data['valid_from'] >= $coupon->valid_from && $coupon_data['valid_from'] <= $coupon->valid_to) {
                    return array('status'=>false, 'message' => 'Kode sudah digunakan pada range tanggal tersebut');
                }
                else if($coupon_data['valid_to'] >= $coupon->valid_from && $coupon_data['valid_to'] <= $coupon->valid_to) {
                    return array('status'=>false, 'message' => 'Kode sudah digunakan pada range tanggal tersebut');
                }
            }

        }

        $coupon = new Coupon();
        foreach ($coupon_data as $key => $value) {
            $coupon->$key = $value;
        }
        return array('status'=>$coupon->save());
    }

    static function getCouponsByCode($coupon_code)
    {
        $coupons = Coupon::where('coupon_code', $coupon_code)
                        ->get();

        return $coupons;
    }


    static function getAllCoupons()
    {
        $coupons = Coupon::get();

        return $coupons;
    }

    static function getValidCouponByCode()
    {

    }

    static function getCouponByCodeAndValitAtDatetime($coupon_code, $datetime_str)
    {
        $key = 'coupon_code_'.$coupon_code;
        $coupon = Cache::get($key);

        if($coupon != null) {
            if($datetime_str >= $coupon->valid_from && $datetime_str <= $coupon->valid_to) {
                return $coupon;
            }

        }
        $coupons = Coupon::where('coupon_code', $coupon_code)
                        ->get();

        foreach($coupons as $key => $coupon) {
            if($datetime_str >= $coupon->valid_from && $datetime_str <= $coupon->valid_to) {
                Cache::put($key, $coupon, 43200);
                return $coupon;
            }
        }
        return null;
    }

    static function getCouponById($coupon_id)
    {
        $key = 'coupon_id_'.$coupon_id;
        $coupon = Cache::get($key);
        if($coupon == null) {
            $coupon = Coupon::find($coupon_id);
            Cache::put($key, $coupon, 43200);
        }

        return $coupon;
    }
}
