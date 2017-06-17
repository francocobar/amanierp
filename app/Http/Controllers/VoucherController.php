<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use VoucherService;
use HelperService;
use App\DiscountVoucherList;
use App\DiscountVoucher;

class VoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('superadmin');
    }

    function addVoucher()
    {
        // dd(HelperService::randomStr());
        return view('voucher.add-vouchers');
    }

    function addVoucherDo(Request $request)
    {
        $inputs = $request->all();
        $inputs['voucher_qty'] = intval($inputs['number_of_vouchers']);
        $inputs['discount_type'] = intval($inputs['discount_type']);
        unset($inputs['number_of_vouchers']);
        unset($inputs['_token']);
        // dd($inputs);
        DB::beginTransaction();
        $voucher = VoucherService::addVoucher($inputs);
        // dd($voucher);
        if($voucher['message'] == '') {
            DB::commit();
            $return_link = "<a href='".route("get.discount.vouchers",['batch'=>$voucher['id']])."'>Lihat Voucher List!</a>";
            // dd($return_link);
            return response()->json([
                'status' => 'success',
                'message' => 'Voucher berhasil ditambahkan! '.$return_link
            ]);
        }
        DB::rollback();
    }

    function validateVoucher()
    {
        return VoucherService::validateVoucher(trim(request()->v));
    }

    function getDiscountVouchers($page=1)
    {
        $take = 20;
        $skip = ($page - 1) * $take;
        $discount_vouchers = DiscountVoucherList::with(['voucherHeader'])->whereNull('claimed_at')
                        ->orderBy('id','desc');

        // $role_user = UserService::getRoleByUser();
        $voucher_header = null;
        if(request()->batch) {
            $discount_vouchers = $discount_vouchers->where('voucher_id', request()->batch);
            $voucher_header = DiscountVoucher::find(request()->batch);
        }
        $total = $discount_vouchers->count();
        $discount_vouchers = $discount_vouchers
                        ->skip($skip)->take($take)->get();
        if($discount_vouchers->count()) {
            return view('voucher.vouchers',[
                'voucher_header' => $voucher_header,
                'discount_vouchers' => $discount_vouchers,
                'message' => HelperService::dataCountingMessage($total, $skip+1, $skip+$discount_vouchers->count(), $page),
                'total_page' => ceil($total/$take),
            ]);
        }


        abort(404);
    }
}
