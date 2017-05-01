<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Employee;
use App\Member;
use App\BranchStock;
use App\Branch;
use App\RentingData;
use App\TransactionHeader;
use App\TransactionDetail;
use HelperService;
use EmployeeService;
use Sentinel;
use Illuminate\Support\Facades\DB;
use Constant;
use ItemService;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager');
    }

    function getCashierPelunasan()
    {
        $invoice_id = str_replace('-','/',trim(request()->invoice));

        $header = TransactionHeader::where('invoice_id',$invoice_id)->first();
        if($header && $header->is_debt && $header->payment2_date==null) {
            return view('cashier.pelunasan',[
                'header' => $header
            ]);
        }
        abort(404);
    }

    function doPelunasan(Request $request, $invoice_id)
    {
        // dd($request->all());
        $invoice_id = str_replace('-','/',trim($invoice_id));

        $header = TransactionHeader::where('invoice_id',$invoice_id)->first();
        if($header && $header->is_debt && $header->payment2_date==null) {
            $inputs = $request->all();

            $param['total_paid2'] = intval(HelperService::unmaskMoney($inputs['total_paid2']));
            if($param['total_paid2'] >= $header->debt) {
                $header->total_paid2 = $param['total_paid2'];
                $header->change2 =  $param['total_paid2']-$header->debt;
                $header->payment2_date = Carbon::Now();
                $header->cashier2_user_id = Sentinel::getUser()->id;
                $header->save();

                $redirect_to = env('PRINT_URL').str_replace('/','-',$invoice_id).'?redirect_back=2';
                if(!isset($inputs['print'])) {
                    $redirect_to = route('search.invoice.report',[
                        'invoice' => $invoice_id
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'redirect_to' => $redirect_to
                ]);
            }
            return response()->json([
                'status' => 'error',
                'need_reload' =>true,
                'message' => 'Kesalahan input, halaman akan reload dan coba lagi!'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'need_reload' =>true,
            'message' => 'Transaksi tidak ditemukan!'
        ]);
    }

    function getCashier()
    {
        $employee_data = EmployeeService::getEmployeeByUser();

        $branch_id = $employee_data != null ? $employee_data->branch_id : 0;
        if($branch_id == 0) {
            if(empty(request()->branch)) {
                return view('cashier.choose_branch',[
                    'branches' => Branch::all(),
                ]);
            }
            else {
                $branch_id = intval(request()->branch);
            }
        }
        $branch = Branch::find($branch_id);
        // dd($branch);

        return view('cashier.apps',[
            'employee_data' => $employee_data,
            'branch' => $branch
        ]);
    }

    function doTransaction(Request $request)
    {
        $inputs = $request->all();
        // dd($inputs);
        if(!isset($inputs['list_inputs']) || count($inputs['list_inputs']) ==0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Harap masukkan minimal satu item.',
                // 'need_reload' => true
            ]);
        }
        // dd(empty("  "));
        // dd($inputs);
        $all_inputs = [];
        $headers =  [];
        //create_header
        $headers['grand_total_item_price'] = intval($inputs['total_temp']);//total price semua item sebelum diskon;
        $headers['total_item_discount'] = intval($inputs['discount_temp']);//total item diskon;
        $employee_data = EmployeeService::getEmployeeByUser();
        $branch_id = $employee_data != null ? $employee_data->branch_id : 0;
        if($branch_id==0) {
            $branch_id =  intval(Crypt::decryptString($inputs['cashier_branch_temp']));
        }
        $headers['branch_id'] = $branch_id;
        $prefix_invoice = HelperService::getPrefixInvoice($headers['branch_id']);
        $number_id = 1;

        $last_invoice = TransactionHeader::where('branch_id', $headers['branch_id'])
                                    ->where('invoice_id','like',$prefix_invoice.'%')
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        if($last_invoice != null) {
            $last_number_id = str_replace($prefix_invoice,'',$last_invoice->invoice_id);
            $number_id = $last_number_id+1;
        }

        $headers['invoice_id'] = $prefix_invoice.sprintf("%05d", $number_id);

        $headers['cashier_user_id'] = Sentinel::getUser()->id;

        $headers['discount_total_fixed_value'] = 0;
        if(!empty(trim($inputs['discount_total_temp']))) {
            $headers['discount_total_input'] = intval($inputs['discount_total_temp']);
            $potongan_total = 0;
            if(trim($inputs['discount_total_type_temp']) =="persen") {
                $headers['discount_total_type'] = 1;
                $potongan_total = $headers['discount_total_input']/100*$headers['grand_total_item_price'];
            }
            else {
                $headers['discount_total_type'] = 2;
                $potongan_total = $headers['discount_total_input'];
            }

            $headers['discount_total_fixed_value'] = $potongan_total;
            if($potongan_total!=intval($inputs['discount_total_fixed_temp'])) {
                //kalo hasil kali server sama client beda, batalkan semua minta input ulang
                return "beda woy";
            }
        }
        $headers['others'] = intval($inputs['others_temp']);

        $total_fix = $headers['grand_total_item_price']+$headers['others']-$headers['total_item_discount']-$headers['discount_total_fixed_value'];
        $headers['total_paid'] = intval($inputs['total_paid_temp']);

        $headers['payment_type'] = 1;
        if(intval($inputs['payment_type_temp']) >=2 && intval($inputs['payment_type_temp']) <= 4) {
            $headers['payment_type'] = intval($inputs['payment_type_temp']);
            $headers['is_debt'] = intval($inputs['payment_type_temp']) == 2;
            if($headers['is_debt']) {
                $headers['debt'] = $total_fix-$headers['total_paid'];
            }
        }
        else {
            $headers['change'] = $headers['total_paid']-$total_fix;
            if($headers['change']<0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jumlah yang dibayarkan kurang.',
                    // 'need_reload' => true
                ]);
            }
        }
        if(isset($inputs['member_temp']) && trim($inputs['member_temp']) != '') {
            $headers['member_id'] = trim($inputs['member_temp']);
        }


        DB::beginTransaction();
        $transaction_header = TransactionHeader::create($headers);
        $flag_total_item_price = $flag_total_item_discount = 0;
        foreach ($inputs['list_inputs'] as $key => $list_input) {
            $new_detail = explode('|', $list_input);
            if(count($new_detail) != 9) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error1. Halaman akan reload dan harap coba lagi!',
                    'need_reload' => true
                ]);
            }
            // dd($new_detail);
                // new detail
                // 0 item id
                // 1 item price @
                // 2 item qty
                // 3 item disc nullable
                // 4 item disc type nullable
                // 5 nilai pasti potongan nullable
                // 6 id pic jika jasa nullable
                // 7 date jika sewa nullable
                // 8 branch id tempat ambil jika sewa nullable
            // $input_item = explode('|', $list_input);
            // dd($input_item);
            $details = [];
            $details['header_id'] = $transaction_header->id;
            $details['item_id'] = $new_detail[0];
            $details['item_price'] = intval($new_detail[1]);
            $item = Item::where('item_id', $details['item_id'])->first();
            $details['item_qty'] = intval($new_detail[2]);
            if($item->item_type == Constant::type_id_produk) {
                //update stok
                $branch_stock = BranchStock::where('branch_id', $headers['branch_id'])
                                                ->where('item_id', $item->item_id)->first();
                $branch_stock->stock = $branch_stock->stock-$details['item_qty'];
                if($branch_stock->stock < 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error3. Halaman akan reload dan harap coba lagi!',
                        'need_reload' => true
                    ]);
                }
                else {
                    $branch_stock->save();
                }
            }
            else if($item->item_type == Constant::type_id_jasa){
                if(!empty(trim($new_detail[6]))) {
                    $details['item_pic'] = $new_detail[6];
                    $incentive = $item->jasaIncentive;
                    $details['pic_incentive'] = $incentive == null ? 0 : $incentive->incentive;

                    $param = [];
                    $param['item_id_jasa'] = $item->item_id;
                    $param['branch_id'] = $headers['branch_id'];
                    $param['qty'] = $details['item_qty'];
                    $update_branch_stock = ItemService::updateBranchStockByJasa($param);
                    if($update_branch_stock != '') {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Error7. Gagal update stok. Halaman akan reload dan harap coba lagi!',
                            'need_reload' => true
                        ]);
                    }
                }
                else {
                    //kalo gak ada pic reload
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error5. Ada data yang kurang. Halaman akan reload dan harap coba lagi!',
                        'need_reload' => true
                    ]);
                }
            }
            else if($item->item_type == Constant::type_id_sewa) {
                if(!empty(trim($new_detail[8])) && !empty(trim($new_detail[7]))) {
                    $renting_data = [];
                    $renting_data['renting_date'] = HelperService::createDateFromString($new_detail[7]);
                    $renting_data['renting_branch'] = intval($new_detail[8]);
                    $renting_data['item_id'] = $details['item_id'];
                    $renting_data['qty'] = $details['item_qty'];
                    $renting_data['transaction_id'] = $details['header_id'];
                    RentingData::create($renting_data);
                }
                else {
                    //kalo gak ada tanggal atau cabang sewa
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error6. Ada data yang kurang. Halaman akan reload dan harap coba lagi!',
                        'need_reload' => true
                    ]);
                }
                // dd($new_detail[7]);
            }
            $details['item_total_price'] = $details['item_price'] * $details['item_qty'];
            $flag_total_item_price += $details['item_total_price'];
            if(!empty(trim($new_detail[3]))) {
                //kalo gak kosong berarti ada diskon
                $details['item_discount_input'] = intval($new_detail[3]);
                $potongan = 0;
                if(trim($new_detail[4]) =="persen") {
                    $details['item_discount_type'] = 1;
                    $potongan = $details['item_discount_input']/100*$details['item_total_price'];
                }
                else {
                    $details['item_discount_type'] = 2;
                    $potongan = $details['item_discount_input'];
                }

                $details['item_discount_fixed_value'] = $potongan;
                if($potongan!=intval($new_detail[5])) {
                    //kalo hasil kali server sama client beda, batalkan semua minta input ulang
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error2. Halaman akan reload dan harap coba lagi!',
                        'need_reload' => true
                    ]);
                }
                $flag_total_item_discount += $potongan;
            }

            $transaction_detail = TransactionDetail::create($details);
            // $all_inputs[] = $details;
        }
        if($flag_total_item_price!=$headers['grand_total_item_price'] || $flag_total_item_discount!=$headers['total_item_discount']) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error4. Halaman akan reload dan harap coba lagi!',
                'need_reload' => true
            ]);
        }
        // dd($inputs);
        // return $inputs['discount_total_temp'];

        DB::commit();
        $redirect_to = env('PRINT_URL').str_replace('/','-',$transaction_header->invoice_id).'?redirect_back=1';
        return response()->json([
            'status' => 'success',
            'redirect_to' => $redirect_to
        ]);
        return $transaction_header->invoice_id;
    }
    function getItems($branch)
    {
        $branch_id = intval(Crypt::decryptString($branch));
        // return $branch_id;
        $return = Item::where('item_name', 'like', '%'.request()->term.'%')
                        ->orWhere('item_id', 'like', '%'.request()->term.'%')
                        ->orderBy('item_name')
                        ->with(['branchStock' => function ($query) use ($branch_id) {
                            $query->where('branch_id', $branch_id);
                        }])
                        ->get();

        return response()->json($return);
    }

    function getPic()
    {
        // dd(Crypt::decryptString($branch));
        $employees = Employee::where('full_name', 'like', '%'.request()->term.'%')
                        ->orWhere('employee_id', 'like', '%'.request()->term.'%')
                        ->orderBy('full_name')
                        ->get(['employee_id', 'full_name'])
                        ->toArray();

        return response()->json($employees);
    }

    function getMembers()
    {
        $members = Member::where('full_name', 'like', '%'.request()->term.'%')
                        ->orWhere('member_id', 'like', '%'.request()->term.'%')
                        ->get(['member_id', 'full_name'])->toArray();

        return response()->json($members);
    }

    function getBranches($item_id=null, $date_to_rent=null)
    {
        // return "oke";
        //branch untuk sewa
        $term = trim(request()->term);
        if($item_id != null & $date_to_rent != null) {
            $branch_return = [];
            $branch_stocks = BranchStock::where('item_id',$item_id)
                                    ->where('stock','>',0)->get();

            foreach ($branch_stocks as $branch_stock) {
                $not_available_count = RentingData::where('renting_date',HelperService::createDateFromString($date_to_rent))
                                        ->where('renting_branch', $branch_stock->branch_id)
                                        ->where('item_id', $item_id)->sum('qty');
                $available = $branch_stock->stock - $not_available_count;

                if($available>=1) {
                    $branch_return_temp = [];
                    $branch_return_temp['branch_name']= Branch::find($branch_stock->branch_id)->branch_name;
                    if(strlen($term)==0 || strpos(strtolower($branch_return_temp['branch_name']), strtolower($term)) !== false) {
                        $branch_return_temp['branch_id']= $branch_stock->branch_id;
                        $branch_return_temp['branch_available']=$available;
                        $branch_return[] = $branch_return_temp;
                    }
                }
            }

            // return $branch_ids;
            // dd($branch_return);
            return response()->json($branch_return);
            $branch = Branch::whereIn('id',$branch_ids)->get(['id','branch_name'])->toArray();

            return response()->json($branch);
        }

    }
}
