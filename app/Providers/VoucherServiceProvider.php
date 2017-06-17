<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\DiscountVoucherList;
use App\DiscountVoucher;
use Sentinel;
use HelperService;
use Carbon\Carbon;

class VoucherServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    static function addVoucher($param)
    {
        $return = [];
        $return['message'] = '';
        $param['created_by'] = Sentinel::getUser()->id;
        $create = DiscountVoucher::create($param);
        if($create) {
            $return['id'] = $create->id;
            $flag = $new_list = [];
            for ($i=0; $i < $param['voucher_qty']; $i++) {
                $duplicate = true;
                while($duplicate) {
                    try {
                        $duplicate=false;
                        $new_code = strtoupper(HelperService::randomStr());
                        while(in_array($new_code, $flag)) {
                            $new_code = strtoupper(HelperService::randomStr());
                        }
                        $flag[]= $new_list['voucher_code']= $new_code;
                        $new_list['voucher_id'] = $return['id'];
                        DiscountVoucherList::create($new_list);
                    }
                    catch(\Exception $exception) {
                        $duplicate = true;
                    }
                }
            }
        }

        return $return;
    }

    static function validateVoucher($voucher_code_input, $claimed=false)
    {
        $return = [];
        $return['voucher_code_input'] = $voucher_code_input;

        $voucher = DiscountVoucherList::with(['voucherHeader'])
                    ->where('voucher_code',$return['voucher_code_input'])->first();
        if($voucher) {
            if($voucher->claimed_at == null) {
                $return['message'] = '';
                $return['discount_value'] = intval($voucher->voucherHeader->discount_value);
                $return['discount_type'] = $voucher->voucherHeader->discount_type;
                if($claimed)  {
                    $voucher->claimed_at = Carbon::now();
                    $voucher->save();
                }
            }
            else {
                $return['message'] = 'Kode Voucher sudah pernah digunakan!';
            }
        }
        else {
            $return['message'] = 'Kode Voucher tidak ditemukan!';
        }
        return $return;
    }
}
