<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Model\Cashier\HeaderOngoing;
use App\Model\Transaction\TrxHeader;
use App\Model\Payment\Payment;
use App\TransactionDetail;
use App\Branch;
use App\Member;
use App\Helper\CouponHelper;
use UserService;
use EmployeeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $error_message = '';
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }

    protected function paymentOngoingTrans($branch, $header)
    {

        if($header->already_finish == 0 && $header->branch_id == $branch->id && ($header->cashier_user_id == Sentinel::getUser()->id || UserService::isSuperadmin())) {
            $data = [];
            $data['title'] = 'Cashier - Pembayaran';
            $data['headline'] = 'Cashier - Pembayaran';
            return view('cashier.rev.payment-ongoing-trans', $data);
        }
        abort(404);
    }

    protected function paymentTrans($branch, $trx_header)
    {
        $payments = Payment::where('payment_prefix_code', 'trx')
                    ->where('payment_code',$trx_header->id)->get();
        $already_paid = 0;
        foreach ($payments as $key => $payment) {
            $already_paid += $payment->nominal_to_pay;
        }

        $data = [];
        $data['title'] = 'Cashier - Pembayaran';
        $data['headline'] = 'Cashier - Pembayaran';
        // dd($trx_header);
        $data['amount'] = $trx_header->total;
        $data['already_paid'] = $already_paid;
        $data['trx_header'] = $trx_header;
        $data['payments'] = $payments;
        return view('cashier.rev.payment-trans', $data);
        dd($already_paid);
        abort(404);
    }

    protected function payOngoingTrans($branch, $header)
    {
        if($header->already_finish == 0 && $header->branch_id == $branch->id && ($header->cashier_user_id == Sentinel::getUser()->id || UserService::isSuperadmin())) {
            DB::beginTransaction();
            $total_tagihan = 0;
            $list_item = Cache::get('list_item_ongoing'.$header->id);
            if(!$list_item) {
                abort(404);
            }
            foreach ($list_item as $key => $item) {
                $total_tagihan = $total_tagihan + ($item['item_qty'] * $item['item_price']);
            }
            $discount_value = 0;
            if($header->coupon_id) {
                $applied_coupon = CouponHelper::getCouponById($header->coupon_id);
                $is_discount = $applied_coupon->disc_value_type == 1;
                if($is_discount) {
                    $discount_value = $applied_coupon->disc_value / 100 * $total_tagihan;
                    if($applied_coupon->max_fix_value != null && $applied_coupon->max_fix_value>0) {
                        if($discount_value > $applied_coupon->max_fix_value) {
                            $discount_value = $applied_coupon->max_fix_value;
                        }
                    }
                }
                else {
                    $discount_value = $applied_coupon->disc_value;
                }
            }

            $new_trx = new TrxHeader();
            $new_trx->id = $header->id;
            $new_trx->sub_total_harga_item = $total_tagihan;
            $new_trx->sub_total_diskon_item = 0;
            $new_trx->sub_total = $new_trx->sub_total_harga_item-$new_trx->sub_total_diskon_item;
            $new_trx->discount2 = $discount_value;
            $new_trx->total = $new_trx->sub_total-$new_trx->discount2;
            $new_trx->cashier_user_id = $header->cashier_user_id;
            $new_trx->branch_id = $branch->id;
            $now_time = Carbon::now();
            $date_time_string = $now_time->toDateTimeString();
            $new_trx->created_at = $date_time_string;
            $new_trx->payment_code = $new_trx->id;
            $new_trx->save();
            // dd(request()->all());
            $new_payment = new Payment();
            $new_payment->payment_prefix_code = 'trx';
            $new_payment->payment_code = $header->id;
            $new_payment->nominal_to_pay = request()->amount_to_pay;
            $new_payment->cash_given = request()->amount_to_cashier;
            $new_payment->change = $new_payment->cash_given-$new_payment->nominal_to_pay;
            if($new_payment->change<0) {
                DB::rollback();
            }
            $new_payment->payment_method = request()->payment_method;
            $new_payment->branch_id = $new_trx->branch_id;
            $new_payment->cashier_user_id = $new_trx->cashier_user_id;
            $new_payment->created_at = $date_time_string;
            $new_payment->save();

            $header->already_finish=1;
            $header->save();
            DB::commit();
        }
        abort(404);
    }
}
