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
use App\EmployeeIncentive;
use App\DetailQtyLog;
use App\Item;
use Illuminate\Support\Facades\DB;
use HelperService;
use UserService;
use App\Log;
use \Carbon\Carbon;

class TransController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager')->except(['changeStatus']);
        // $this->middleware('superadmin')->only(['changeStatus']);
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
        // dd($inputs['qty_done']);

        // dd('stop');
        $payment_flag = Crypt::decryptString($inputs['payment']);
        $payment_flag_id = str_replace('Payment-','',$payment_flag);
        if($payment_flag_id != trim($inputs['ongoing_trans_id'])) {
            return redirect()->route('get.cashier.v2');
        }

        DB::beginTransaction();
        $header = TransactionHeader::find(trim($inputs['ongoing_trans_id']));
        $cashier = Sentinel::getUser();
        $details = TransactionDetail::where('header_id', $header->id)->with(['itemInfo'])->get();
        foreach ($details as $key => $detail) {
            $detail->item_qty_done = $inputs['qty_done'][$detail->id];
            if($detail->item_qty > $inputs['qty_done'][$detail->id]) {
                $detail->claim_status = 1;
            }
            else if($detail->item_qty == $inputs['qty_done'][$detail->id]) {
                $detail->claim_status = 2;
            }
            if($detail->itemInfo && $detail->itemInfo->item_type==2) {
                // dd($detail->itemInfo->jasaIncentive);
                $detail->pic_incentive = 0;
                for($i=0; $i<$detail->item_qty_done; $i++)
                {
                    $emp_incentive = new EmployeeIncentive();
                    $emp_incentive->detail_id = $detail->id;
                    $emp_incentive->employee_id = '';
                    $emp_incentive->incentive = $detail->itemInfo->jasaIncentive->incentive;
                    $emp_incentive->branch_id = $header->branch_id;
                    $emp_incentive->save();
                }
            }
            $detail->save();
        }
        if($header->status != 1 || $header->cashier_user_id != $cashier->id)
        {
            return redirect()->route('get.cashier.v2');
        }
        if(!isset($inputs['total_paid']) || empty(trim($inputs['total_paid']))) {
            $inputs['total_paid'] = 0;
        }
        $total_paid = HelperService::unmaskMoney($inputs['total_paid']);
        $total_trans = $header->totalTransaction();
        if(isset($inputs['lunas']))
        {
            if($total_paid<$total_trans)
            {
                return "Pembayaran tidak cukup";
            }
            $header->paid_value = $total_trans;
            $header->change = $total_paid-$total_trans;
        }
        else {

            $header->paid_value = HelperService::unmaskMoney($inputs['paid_value']);
            $header->debt = $total_trans-$header->paid_value;
            $header->is_debt = true;
            $header->change = $total_paid-$header->paid_value;
        }

        $header->total_paid = $total_paid;
        $header->payment_type =  $inputs['payment_type'];
        $header->status = 2;
        $header->save();
        if(isset($inputs['ke_galeri']))
        {
            $ke_galeri = HelperService::unmaskMoney($inputs['ke_galeri']);
            // dd($ke_galeri);
            // $header = '';
            $this->createTagihanKeGaleri($header, $ke_galeri);
            DB::commit();
        }
        else {
            DB::commit();
        }


        $to = '';
        if(UserService::isSuperadmin())
        {
            $to = '&b='.$header->branch_id;
        }
        if(env('APP_ENV') != 'local') {
            $redirect_to = env('PRINT_URL').str_replace('/','-',$header->invoice_id).'?redirect_back=1'.$to;
            return redirect($redirect_to);
        }
        $qs = [];
        if(UserService::isSuperadmin()) {
            $qs = ['branch'=>$header->branch_id];
        }
        return redirect()->route('get.cashier.v2',$qs);
    }


    function changeStatus(Request $request)
    {
        if(!Sentinel::getUser()->hasAccess(['changeStatus.trans']))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Halaman akan reload!',
                'need_reload' => true
            ]);
        }
        $inputs = $request->all();
        if(empty($inputs['log']) || empty($inputs['new_status']) || empty($inputs['header_id']))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad Request. Halaman akan reload!',
                'need_reload' => true
            ]);
        }
        $header = TransactionHeader::find($inputs['header_id']);

        if($header)
        {
            $message = 'Transaksi berhasil di';
            if($inputs['new_status'] == '3')
            {
                if(true)
                {
                    $header_date = Carbon::create($header->created_at->year, $header->created_at->month, $header->created_at->day,0,0,0);
                    if(Carbon::today()->diffInDays($header_date) > 0)
                    {
                        return response()->json([
                            'status' => 'error',
                            'message' => '<b style="color:red;">Gagal Menghapus</b>. Transaksi yang sudah lewat tanggal tidak dapat dihapus!',
                            'need_reload' => true
                        ]);
                    }
                }
                $header->status = 3;
                $message.='hapus';
            }
            else if($inputs['new_status'] == '4') {
                $header->status = 4;
                $message.='batalkan';
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error. Halaman akan reload dan harap coba lagi!',
                    'need_reload' => true
                ]);
            }
            DB::beginTransaction();
            try {
                TransactionDetail::where('header_id',$header->id)->update(['claim_status'=>3]);
                $header->status_changed_by = Sentinel::getUser()->id;

                $log = new Log();
                $log->log_text = trim($inputs['log']);
                $log->log_by = Sentinel::getUser()->id;
                if($inputs['new_status'] == '3') {
                    $log->log_for = 'remove trx '.$header->id;
                }
                else if($inputs['new_status'] == '4') {
                    $log->log_for = 'cancel trx '.$header->id;
                }
                $log->save();
                $header->log_status_change = $log->id;
                $header->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                return false;
            }
            //create log
            return response()->json([
                'need_reload' => true,
                'status' => 'success',
                'message' => $message,
                'no_reset_form' => true
            ]);

        }

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
            $header->status_changed_by = Sentinel::getUser()->id;
            $log = new Log();
            $log->log_text = 'sebelum selesai pembayaran';
            $log->log_by = Sentinel::getUser()->id;
            $log->log_for = 'remove trx '.$header->id;
            $log->save();
            $header->log_status_change = $log->id;
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
        $data['details'] = TransactionDetail::where('header_id',$header->id)->with(['itemInfo'])->get();
        // dd($data['details']);
        return view('cashier.v2.ongoing-trans-payment', $data);
        return "do payment";
    }

    function updateItemTrans(Request $request)
    {
        $inputs = $request->all();
        // $cashier = Sentinel::getUser();
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
            $grand_total_item_price = 0;
            $total_item_discount = 0;
            if(isset($inputs['item_id'])) {
                foreach ($inputs['item_id'] as $key => $item_id) {
                    $new_detail = TransactionDetail::where('header_id',$trans_id)
                                    ->where('item_id', trim($item_id))
                                    ->first();
                    if($new_detail==null)
                    {
                        $new_detail = new TransactionDetail();
                        $new_detail->header_id = $trans_id;
                        $item = $item = Item::where('item_id', trim($item_id))->first();
                        if($item) {
                            $new_detail->item_id = $item->item_id;
                            if($header->member_id) {
                                $new_detail->item_price = $item->m_price;
                            }
                            else {
                                $new_detail->item_price = $item->nm_price;
                            }
                        }
                        else {
                            $new_detail->item_id = $item_id;
                            $idx_name = 'item_name_'.trim($item_id);
                            $flag_name = isset($inputs[$idx_name]) && !empty(trim($inputs[$idx_name]));
                            $idx_price = 'item_price_'.trim($item_id);
                            $flag_price = isset($inputs[$idx_price]) && !empty(trim($inputs[$idx_price]));
                            if($flag_name && $flag_price)
                            {
                                $new_detail->custom_name = trim($inputs[$idx_name]);
                                $new_detail->item_price = trim($inputs[$idx_price]);
                            }
                            else {
                                DB::rollBack();
                                return response()->json([
                                    'status' => 'error',
                                    'need_reload' => true,
                                    'message' => 'Terjadi kesalahan!'
                                ]);
                            }
                        }
                    }
                    $new_detail->item_qty = $inputs['item_qty'][$key];
                    $new_detail->item_total_price = $new_detail->item_price*$new_detail->item_qty;
                    $new_detail->item_discount_input = null;
                    $new_detail->item_discount_type = null;
                    $new_detail->item_discount_fixed_value = 0;
                    $idx_input = 'discount_'.$new_detail->item_id;
                    if(isset($inputs[$idx_input]) && intval(trim($inputs[$idx_input])) > 0) {
                        $inputs[$idx_input] = HelperService::unmaskMoney($inputs[$idx_input]);
                        $idx_type = 'discount_type_'.$new_detail->item_id;
                        if(isset($inputs[$idx_type]) && trim($inputs[$idx_type]) == '%') {
                            $percentage = intval(trim($inputs[$idx_input]));
                            $new_detail->item_discount_input = $percentage;
                            $new_detail->item_discount_type = 1;
                            $new_detail->item_discount_fixed_value = $percentage/100 * $new_detail->item_total_price;
                        }
                        else {
                            $new_detail->item_discount_input = intval(trim($inputs[$idx_input]));
                            $new_detail->item_discount_fixed_value = intval(trim($inputs[$idx_input]));
                            $new_detail->item_discount_type = 2;
                        }
                    }
                    $new_detail->save();
                    $still_exist[] = $new_detail->item_id;
                    $total_item_discount = $total_item_discount + $new_detail->item_discount_fixed_value;
                    $grand_total_item_price = $grand_total_item_price + $new_detail->item_total_price;

                }
                $header->total_item_discount = $total_item_discount > 0 ? $total_item_discount : null;
                $header->grand_total_item_price = $grand_total_item_price;
                $total = $grand_total_item_price - $total_item_discount;
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
                else {
                    $header->discount_total_input = null;
                    $header->discount_total_type=null;
                    $header->discount_total_fixed_value = 0;
                }
            }
            else
            {
                $header->grand_total_item_price = 0;
                $header->discount_total_input = null;
                $header->discount_total_type=null;
                $header->discount_total_fixed_value = 0;
                $header->total_item_discount = null;
                $header->others = 0;
            }
            $header->save();

            TransactionDetail::where('header_id',$trans_id)
                           ->whereNotIn('item_id', $still_exist)
                           ->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'cashier' => true,
                'no_reset_form' => true
            ]);
        }

        return isset($inputs['ongoing_trans_id']) ? $inputs['ongoing_trans_id'] : "gak ada";
    }

    function addItemTrans(Request $request)
    {
        $inputs = $request->all();
        if(isset($inputs['ongoing_trans_id']) && !empty(trim($inputs['ongoing_trans_id'])))
        {
            $trans_id = trim($inputs['ongoing_trans_id']);
            return redirect()->route('get.cashier.ongoing',['trans_id'=>$trans_id]);
        }
        abort(404);
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
        $is_superadmin = UserService::isSuperadmin();
        if($branch_id == 0 || $is_superadmin)
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
        if(!$is_superadmin && $add_trans_parse_decrypt!=$cashier->first_name.'-'.$branch_id)
        {
            abort(404);
        }
        $data['branch'] = Branch::find($branch_id);

        $inputs['branch_id'] = $branch_id;
        $inputs['cashier_id'] = $cashier->id;
        // dd(trim($inputs['add_trans_type']));
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

        else if(trim($inputs['add_trans_type']) == '3')
        {
            if(isset($inputs['add_trans_for_branch']) && !empty(trim($inputs['add_trans_for_branch']))) {
                $branch = Branch::find($inputs['add_trans_for_branch']);
                // dd($inputs['add_trans_member']);
                if($branch)
                {
                    $inputs['branch'] = $branch;
                    $this->addNewTrans($inputs, 3);
                    return redirect()->route('get.cashier.v2',$query_string);
                }
            }
            return view('cashier.v2.add-trans-branch', $data);
        }
        abort(404);
    }

    function addNewTrans($param, $type=1)
    {
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

        else if($type==3)
        {
            $branch = $param['branch'];
            $new_trans->member_id = $branch->id;
            $new_trans->customer_name = 'Cabang '.$branch->branch_name;
            $new_trans->customer_phone = $branch->phone;
            $new_trans->antar_cabang = true;
        }
        $new_trans->save();
        //belum milih
        return $new_trans;
    }

    function createTagihanKeGaleri($header, $total)
    {
        $param['cashier_id'] = $header->cashier_user_id;
        $param['branch_id'] = $header->branch_id;
        $param['add_trans_guest_name'] = 'Amanie Galeri';
        $param['add_trans_guest_phone'] = '07367324812';
        $tagihan_header= $this->addNewTrans($param, 2);
        $tagihan_header->ke_galeri = true;
        $tagihan_header->is_debt = true;
        $tagihan_header->debt = $total;
        $tagihan_header->grand_total_item_price = $total;
        $tagihan_header->status=2;
        $tagihan_header->save();
        // echo $tagihan_header->id;

        $new_detail = new TransactionDetail();
        $new_detail->header_id = $tagihan_header->id;
        $new_detail->item_id = 1;
        $new_detail->item_price = $total;
        $new_detail->custom_name = 'Tagihan Trx #'.$header->id.' '. $header->invoice_id;
        $new_detail->item_qty = 1;
        $new_detail->item_total_price = $new_detail->item_price*$new_detail->item_qty;
        $new_detail->item_discount_input = null;
        $new_detail->item_discount_type = null;
        $new_detail->item_discount_fixed_value = 0;
        $new_detail->save();
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

    function pendingTrans()
    {
        if(request()->b && request()->key)
        {
            $branch = Branch::find(Crypt::decryptString(request()->b));
            if($branch) {
                $header = null;
                if(is_numeric(request()->key)) {
                    $header = TransactionHeader::find(intval(request()->key));
                }
                else {
                    $invoice_id = str_replace('-','/',trim(request()->key));
                    $header = TransactionHeader::where('invoice_id', $invoice_id)->first();
                }
                if($header) {
                    $data['branch'] = $branch;
                    $data['details'] = TransactionDetail::where('header_id',$header->id)->with(['employeeIncentives'])->get();
                    $data['header'] = $header;
                    return view('cashier.v2.pending-trans', $data);
                }
                else {
                    abort(404);
                }
            }

        }
        abort(404);
    }

    function updateQtyPendingTrans(Request $request)
    {
        $inputs = $request->all();
        DB::beginTransaction();
        $other_req = decrypt($inputs['params']);
        $branch = Branch::find($other_req['branch_id']);
        if(!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error1. Terjadi kesalahan.',
                'need_reload' => true
            ]);
        }
        $flag_header = $other_req['header_id'];
        $now_user = Sentinel::getUser();
        foreach($inputs as $key => $value) {
            if(is_numeric($value)) {
                if(intval($value) > 0) {
                    $data = decrypt($key);
                    $detail = TransactionDetail::find($data['detail_id']);

                    if($detail && $detail->header_id ==$flag_header && $detail->item_qty-$detail->item_qty_done == $data['qty_max'] && intval($value)<=$data['qty_max']) {
                        $detail->item_qty_done = $detail->item_qty_done + intval($value);
                        if($detail->itemInfo && $detail->itemInfo->item_type==2) {
                            // dd($detail->itemInfo->jasaIncentive);
                            $detail->pic_incentive = 0;
                            for($i=0; $i<intval($value); $i++)
                            {
                                $emp_incentive = new EmployeeIncentive();
                                $emp_incentive->detail_id = $detail->id;
                                $emp_incentive->employee_id = '';
                                $emp_incentive->incentive = $detail->itemInfo->jasaIncentive->incentive;
                                $emp_incentive->branch_id = $branch->id;
                                $emp_incentive->save();
                            }
                        }
                        $detail->save();
                        $log = new DetailQtyLog();
                        $log->detail_id = $detail->id;
                        $log->log_by = $now_user->id;
                        $log->log_text = 'qty selesai +'.$value;
                        $log->save();
                    }
                    else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Error2. Terjadi kesalahan.',
                            'need_reload' => true
                        ]);
                    }
                }
            }
        }
        DB::commit();
        return response()->json([
            'need_reload' => true,
            'status' => 'success',
            'message' => 'Update Qty berhasil!',
            'no_reset_form' => true
        ]);
    }
    function employeeIncentive()
    {
        if(request()->b && request()->key)
        {
            $branch = Branch::find(Crypt::decryptString(request()->b));
            if($branch) {
                $header = null;
                if(is_numeric(request()->key)) {
                    $header = TransactionHeader::find(intval(request()->key));
                }
                else {
                    $invoice_id = str_replace('-','/',trim(request()->key));
                    $header = TransactionHeader::where('invoice_id', $invoice_id)->first();
                }
                if($header) {
                    $data['branch'] = $branch;
                    $data['details'] = TransactionDetail::where('header_id',$header->id)->with(['employeeIncentives'])->get();
                    $data['header'] = $header;
                    return view('cashier.v2.employee-incentive', $data);
                }
                else {
                    abort(404);
                }
            }

        }
        abort(404);
    }

}
