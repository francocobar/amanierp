<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Crypt;
use EmployeeService;
use Sentinel;
use App\Branch;
use App\Member;
use App\TransactionHeader;
use App\TransactionDetail;
use App\Item;
use Illuminate\Support\Facades\DB;
use HelperService;
use UserService;

class TransController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }

    function ongoingTrans($trans_id)
    {
        $header = TransactionHeader::find($trans_id);
        $cashier = Sentinel::getUser();
        if($header->status != 1 || $header->cashier_user_id != $cashier->id)
        {
            abort(404);
        }
        $data['header'] = $header;
        $data['details'] = TransactionDetail::where('header_id', $header->id)
                                                ->where('claim_status','!=', 3)
                                                ->get();
        $data['discount'] = $header->discount_total_input == null ? 0 :  intval($header->discount_total_input);
        $discount_type_id = $header->discount_total_type == null ? 1 : $header->discount_total_type;
        $data['discount_type'] = $discount_type_id==1 ? '%' : '';
        $data['branch'] = Branch::find($header->branch_id);
        $data['query_string'] = [];
        if(UserService::isSuperadmin())
        {
            $data['query_string'] = ['branch' => $header->branch_id];
        }
        return view('cashier.v2.ongoing-trans', $data);
    }

    function doLastStep(Request $request)
    {
        $inputs= $request->all();
        $payment_flag = Crypt::decryptString($inputs['payment']);
        $payment_flag_id = str_replace('Payment-','',$payment_flag);
        if($payment_flag_id != trim($inputs['ongoing_trans_id'])) {
            return redirect()->route('get.cashier.v2');
        }
        $header = TransactionHeader::find(trim($inputs['ongoing_trans_id']));
        $cashier = Sentinel::getUser();
        if($header->status != 1 || $header->cashier_user_id != $cashier->id)
        {
            return redirect()->route('get.cashier.v2');
        }

        $total_paid = HelperService::unmaskMoney($inputs['total_paid']);
        $total_trans = $header->totalTransaction();
        if(isset($inputs['lunas']))
        {
            if($total_paid<$total_trans)
            {
                return "Pembayaran tidak cukup";
            }
            $header->change = $total_paid-$total_trans;
        }
        else {
            $header->debt = $total_trans-$total_paid;
            $header->is_debt = true;
        }

        $header->paid_value = HelperService::unmaskMoney($inputs['paid_value']);
        $header->total_paid = $total_paid;
        $header->payment_type = $inputs['payment_type'];
        $header->status = 2;
        $header->save();
        $qs = [];
        $to = '';
        if(UserService::isSuperadmin())
        {
            $to = '&b='.$header->branch_id;
        }
        $redirect_to = env('PRINT_URL').str_replace('/','-',$header->invoice_id).'?redirect_back=1'.$to;
        return redirect($redirect_to);
        return redirect()->route('get.cashier.v2',$qs);
    }
    function doNextStep(Request $request)
    {
        $inputs= $request->all();
        $header = TransactionHeader::find(trim($inputs['ongoing_trans_id']));
        $cashier = Sentinel::getUser();
        if($header->status != 1 || $header->cashier_user_id != $cashier->id)
        {
            abort(404);
        }
        if($inputs['trans_set_to'] == '3') {
            $header->status = 3;
            $header->save();
            $qs = [];
            if(UserService::isSuperadmin()) {
                $qs = ['branch' => $header->branch_id];
            }
            TransactionDetail::where('header_id',$header->id)->update(['claim_status'=>3]);
            return redirect()->route('get.cashier.v2',$qs);
        }
        $branch_id = $header->branch_id;
        $cashier= Sentinel::getUser();
        // dd($cashier_first_name->first_name);
        $data['header'] = $header;
        $data['branch'] = Branch::find($branch_id);

        return view('cashier.v2.ongoing-trans-payment', $data);
        return "do payment";
    }

    function updateItemTrans(Request $request)
    {
        $inputs = $request->all();
        $cashier = Sentinel::getUser();
        if(isset($inputs['ongoing_trans_id']) && !empty(trim($inputs['ongoing_trans_id'])))
        {
            $trans_id = trim($inputs['ongoing_trans_id']);
            $header = TransactionHeader::find($trans_id);
            $cashier = Sentinel::getUser();
            if($header->status != 1 || $header->cashier_user_id != $cashier->id)
            {
                abort(404);
            }
            $trans_id = $inputs['ongoing_trans_id'];
            $still_exist = [];
            DB::beginTransaction();
            $total = 0;
            if(isset($inputs['item_id'])) {
                foreach ($inputs['item_id'] as $key => $item_id) {
                    $still_exist[] = trim($item_id);
                    $new_detail = TransactionDetail::where('header_id',$trans_id)
                                    ->where('item_id', trim($item_id))
                                    ->first();
                    if($new_detail!=null)
                    {
                        $new_detail->item_qty = $inputs['item_qty'][$key];
                        $new_detail->item_total_price = $new_detail->item_price*$new_detail->item_qty;
                        $total = $total + $new_detail->item_total_price;
                        $new_detail->save();
                    }
                }

                $header->grand_total_item_price = $total;
                $discount = intval(trim($inputs['discount']));

                if($discount>0)
                {
                    if(trim($inputs['discount_type']) == '%')
                    {
                        $header->discount_total_input = $discount;
                        $header->discount_total_type=1;
                        $header->discount_total_fixed_value = $discount/100*$total;
                    }
                    else {
                        $header->discount_total_input = $discount;
                        $header->discount_total_type=2;
                        $header->discount_total_fixed_value = $discount;
                    }
                }
            }
            else
            {
                $header->grand_total_item_price = 0;
                $header->discount_total_input = null;
                $header->discount_total_type=null;
                $header->discount_total_fixed_value = 0;
            }
            $header->save();

            TransactionDetail::where('header_id',$trans_id)
                           ->whereNotIn('item_id', $still_exist)
                           ->delete();

            DB::commit();
            return redirect()->route('get.cashier.ongoing',['trans_id'=>$trans_id]);
        }
    }

    function addItemTrans(Request $request)
    {
        $inputs = $request->all();
        $cashier = Sentinel::getUser();
        if(isset($inputs['ongoing_trans_id']) && !empty(trim($inputs['ongoing_trans_id'])))
        {
            $trans_id = trim($inputs['ongoing_trans_id']);
            $header = TransactionHeader::find($trans_id);
            $cashier = Sentinel::getUser();
            if($header->status != 1 || $header->cashier_user_id != $cashier->id)
            {
                abort(404);
            }

            DB::beginTransaction();
            if(isset($inputs['add_detail_item_id'])) {
                $item = Item::where('item_id', trim($inputs['add_detail_item_id']))->first();

                $new_detail = TransactionDetail::where('header_id',$trans_id)
                                ->where('item_id', $item->item_id)
                                ->first();

                if($new_detail==null)
                {
                    $new_detail = new TransactionDetail();
                    $new_detail->item_qty = $inputs['add_detail_qty'];
                }
                else {
                    $new_detail->item_qty += $inputs['add_detail_qty'];
                }

                $new_detail->header_id = $trans_id;
                $new_detail->item_id = $item->item_id;
                if($header->member_id)
                {
                    $new_detail->item_price = $item->m_price;
                }
                else
                {
                    $new_detail->item_price = $item->nm_price;
                }
                $new_detail->item_total_price = $new_detail->item_price*$new_detail->item_qty;
                $new_detail->save();
            }
            else if(isset($inputs['add_detail_costumize_item'])) {
                $new_detail = new TransactionDetail();
                $new_detail->custom_name = trim($inputs['add_detail_costumize_item']);
                $new_detail->item_qty = $inputs['add_detail_qty'];
                $new_detail->item_id = '-';
                $new_detail->header_id = $trans_id;
                $new_detail->item_price = HelperService::unmaskMoney($inputs['add_detail_price']);
                $new_detail->item_total_price = $new_detail->item_price*$new_detail->item_qty;
                $new_detail->save();
                $new_detail->item_id = $new_detail->id;
                $new_detail->save();
            }







            $details = TransactionDetail::where('header_id', $header->id)->get();
            $total = 0;
            foreach ($details as $key => $detail) {
                $total = $total + $detail->item_total_price;
            }
            $header->grand_total_item_price = $total;
            if($header->discount_total_input!=null && $header->discount_total_input>0) {
                $discount = $header->discount_total_input;
                if($header->discount_total_type==1) {
                    $header->discount_total_fixed_value = $discount/100*$total;
                }
                else {
                    $header->discount_total_fixed_value  = $discount;
                }

            }
            $header->save();
            DB::commit();
            // dd($header->grand_total_item_price);
            return redirect()->route('get.cashier.ongoing',['trans_id'=>$trans_id]);
        }
    }

    function addTrans(Request $request)
    {
        $inputs = $request->all();

        if(!isset($inputs['add_trans_parse']))
        {
            abort(404);
        }

        $employee_data = EmployeeService::getEmployeeByUser();
        $branch_id = $employee_data != null ? $employee_data->branch_id : 0;
        $query_string = [];
        if($branch_id == 0)
        {
            if(isset($inputs['add_trans_branch'])) {
                $branch_id = Crypt::decryptString($inputs['add_trans_branch']);
                $query_string = [
                    'branch' => $branch_id
                ];
            } else {
                abort(404);
            }
        }
        $add_trans_parse_decrypt = Crypt::decryptString($inputs['add_trans_parse']);
        $cashier= Sentinel::getUser();
        // dd($cashier_first_name->first_name);
        if($add_trans_parse_decrypt!=$cashier->first_name.'-'.$branch_id)
        {
            abort(404);
        }
        $data['branch'] = Branch::find($branch_id);

        $inputs['branch_id'] = $branch_id;
        $inputs['cashier_id'] = $cashier->id;
        if(trim($inputs['add_trans_type']) == '1')
        {
            if(isset($inputs['add_trans_member']) && !empty(trim($inputs['add_trans_member']))) {
                $member = Member::where('member_id',trim($inputs['add_trans_member']))->first();
                // dd($inputs['add_trans_member']);
                if($member)
                {
                    $inputs['member'] = $member;
                    $this->addNewTrans($inputs);
                    return redirect()->route('get.cashier.v2',$query_string);
                }
            }
            return view('cashier.v2.add-trans-member', $data);
        }
        else if(trim($inputs['add_trans_type']) == '2')
        {
            if(isset($inputs['add_trans_guest_name']) && !empty(trim($inputs['add_trans_guest_name']))) {
                $this->addNewTrans($inputs, 2);
                return redirect()->route('get.cashier.v2',$query_string);
            }
            return view('cashier.v2.add-trans-guest', $data);
        }
        abort(404);
    }

    function addNewTrans($param, $type=1)
    {
        DB::beginTransaction();
        $new_trans = new TransactionHeader();
        $new_trans->cashier_user_id = $param['cashier_id'];
        $new_trans->branch_id = $param['branch_id'];
        $new_trans->invoice_id = $this->generateInvoiceId($param['branch_id']);
        $new_trans->payment_type = 0;
        if($type==1)
        {
            $member = $param['member'];
            $new_trans->member_id = $member->member_id;
            $new_trans->customer_name = $member->full_name;
            $new_trans->customer_phone = $member->phone;
        }
        else if($type==2)
        {
            $new_trans->customer_name = trim($param['add_trans_guest_name']);
            if(isset($param['add_trans_guest_phone']) && !empty(trim($param['add_trans_guest_phone'])))
            {
                $new_trans->customer_phone = trim($param['add_trans_guest_phone']);
            }
        }
        $new_trans->save();
        //belum milih
        DB::commit();
    }

    function generateInvoiceId($branch_id)
    {
        $prefix_invoice = HelperService::getPrefixInvoice($branch_id);
        $number_id = 1;

        $last_invoice = TransactionHeader::where('branch_id', $branch_id)
                                    ->where('invoice_id','like',$prefix_invoice.'%')
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        if($last_invoice != null) {
            $last_number_id = str_replace($prefix_invoice,'',$last_invoice->invoice_id);
            $number_id = $last_number_id+1;
        }

        return $prefix_invoice.sprintf("%05d", $number_id);
    }

}
