<?php

namespace App\Http\Controllers;

use Closure;
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
use App\NextPayment;
use HelperService;
use StockService;
use EmployeeService;
use VoucherService;
use Sentinel;
use Illuminate\Support\Facades\DB;
use Constant;
use ItemService;
use Carbon\Carbon;
use App\EmployeeTurnover;
use App\PembukuanBranch;
use App\PaketConfiguration;
use App\EmployeeIncentive;

use Session;
class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }

    function getCustomCashier()
    {
        $custom_details = session()->get('custom_details',[]);
        if(request()->remove) {
            if(isset($custom_details[intval(request()->remove-2)])) {
                unset($custom_details[intval(request()->remove-2)]);
                session()->put('custom_details', $custom_details);
                return redirect()->route('get.custom.cashier');
            }
            else if(request()->remove=='all') {
                session()->put('custom_details', []);
                return redirect()->route('get.custom.cashier');
            }
            else{
                abort(404);
            }
        }

        return view('cashier.custom',[
            'custom_details' => $custom_details,
            'no' => 1,
            'employee_data' => EmployeeService::getEmployeeByUser()
        ]);
    }

    function getCustomCashierFinishing()
    {
        $custom_details = session()->get('custom_details',[]);
        if(count($custom_details)>0) {
            $grand_total = 0;
            foreach($custom_details as $custom_detail)
            {
                $grand_total += $custom_detail['item_price'] * $custom_detail['item_qty'] -
                                $custom_detail['item_discount_fixed_value'];
            }
            return view('cashier.custom2',[
                'grand_total' => $grand_total,
                'employee_data' => EmployeeService::getEmployeeByUser()
            ]);
        }
        abort(404);
    }

    function customCashierAddDetail(Request $request)
    {
        $inputs = $request->all();
        $custom_details = session()->get('custom_details',[]);
        $custom_details[] = [
            'custom_name' => $inputs['custom_name'],
            'item_price' => HelperService::unmaskMoney($inputs['item_price']),
            'item_qty' => $inputs['item_qty'],
            'item_discount_fixed_value' => HelperService::unmaskMoney($inputs['item_discount_fixed_value']),
        ];
        session()->put('custom_details', $custom_details);
        return response()->json([
            'status' => 'success',
            'redirect_to' => route('get.custom.cashier')
        ]);
    }

    function getCashierPelunasan()
    {
        $invoice_id = str_replace('-','/',trim(request()->invoice));
        //with menggunakan where
        $header = TransactionHeader::with(['nextPayments'=>function ($query) {
                    $query->orderBy('created_at','desc')->first();
                }])
                ->where('invoice_id',$invoice_id)->first();
        // dd($header);
        if($header && $header->is_debt) {
            if($header->nextPayments && isset($header->nextPayments[0])) {
                if($header->nextPayments[0]->debt_after == 0) {
                    abort(404);
                }
            }

            return view('cashier.pelunasan',[
                'header' => TransactionHeader::where('invoice_id',$invoice_id)->first()
            ]);
        }
        abort(404);
    }

    function searchInvoice()
    {
        $headers = null;
        if(request()->invoice)
        {
            $headers = TransactionHeader::with(['rentingDatas'])->where('invoice_id', 'like', '%'.trim(request()->invoice).'%')->get();
        }
        // dd($headers);
        // dd($headers->count());

        return view('cashier.search-invoice',[
            'headers' => $headers,
            'keyword' => trim(request()->invoice)
        ]);
    }

    function getInvoice()
    {
        $header = TransactionHeader::with(['member','cashier','branch'])->where('invoice_id',
                    request()->param)->first();
        if($header) {
            if(request()->detail_klaim=='1') {
                return view('cashier.get-invoice2',[
                    'header' => $header,
                    'today' => Carbon::now(),
                    'details' => TransactionDetail::with(['itemInfo'])->where('header_id', $header->id)->get()
                ]);
            }
            return view('cashier.get-invoice',[
                'header' => $header,
                'today' => Carbon::now(),
                'details' => TransactionDetail::with(['itemInfo'])->where('header_id', $header->id)->get()
            ]);
        }
        abort(404);
    }

    function doPelunasan(Request $request, $invoice_id)
    {
        // dd($request->all());
        $invoice_id = str_replace('-','/',trim($invoice_id));

        $header = TransactionHeader::where('invoice_id',$invoice_id)->first();
        if($header && $header->is_debt) {
            $inputs = $request->all();

            $next_payments = NextPayment::where('header_id',$header->id);
            $last_debt = $header->debt;
            // dd($next_payments->count());
            if($next_payments->count() >= 1) {
                $next_payments = $next_payments->orderBy('created_at','desc')->first();
                $last_debt = $next_payments->debt_after;
                // dd('oke');
            }

            $paid_value = intval(HelperService::unmaskMoney($inputs['paid_value']));
            if($paid_value <= $last_debt) {
                DB::beginTransaction();
                $new_next_payment = new NextPayment();
                $new_next_payment->header_id = $header->id;
                $new_next_payment->debt_before = $last_debt;
                $new_next_payment->paid_value = $paid_value;
                $new_next_payment->total_paid = intval(HelperService::unmaskMoney($inputs['total_paid2']));
                $new_next_payment->change = $new_next_payment->total_paid-$new_next_payment->paid_value;
                $new_next_payment->debt_after = $new_next_payment->debt_before-$new_next_payment->paid_value;
                $new_next_payment->payment_type = $inputs['payment_type'];

                //cashier
                $cashier_employee = EmployeeService::getEmployeeByUser();
                $new_next_payment->branch_id = 1;
                $new_next_payment->cashier_user_id = Sentinel::getUser()->id;
                $new_next_payment->save();
                if($new_next_payment->debt_after == 0) {
                    $header->last_payment_date = Carbon::Now();
                    $header->save();
                }
                // return "oke";
                $redirect_to = env('PRINT_URL').str_replace('/','-',$invoice_id).'?redirect_back=2';
                if(!isset($inputs['print'])) {
                    $redirect_to = route('search.invoice.cashier',[
                        'invoice' => $invoice_id
                    ]);
                }
                DB::commit();
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

    function getCashier2()
    {
        $employee_data = EmployeeService::getEmployeeByUser();

        // $branch_id = 1;
        $branch_id = $employee_data != null ? $employee_data->branch_id : session()->put('branch_selected');
        if($branch_id == 0 || $branch_id==null) {
            if(empty(request()->branch)) {
                return view('cashier.choose_branch',[
                    'branches' => Branch::all(),
                ]);
            }
            else {
                $branch_id = intval(request()->branch);
            }
        }
        // dd($branch_id);
        $branch = Branch::find($branch_id);
        $transaction_ongoing = TransactionHeader::where('branch_id',$branch_id)->where('status',1)->get();

        // dd($branch);

        return view('cashier.v2.apps-v2',[
            'employee_data' => $employee_data,
            'branch' => $branch,
            'transaction_ongoing' => $transaction_ongoing
        ]);
    }

    function getCashier()
    {
        // abort(404);
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

    function doTransactionCustom(Request $request)
    {
        $custom_details = session()->get('custom_details',[]);
        if(count($custom_details)>0) {
            $inputs = $request->all();
            // dd($inputs);
            $header = [];

            $headers['branch_id'] = 0;
            if(isset($inputs['cashier_branch_temp'])) {
                $headers['branch_id'] =  intval(Crypt::decryptString($inputs['cashier_branch_temp']));
            }
            else {
                $employee_data = EmployeeService::getEmployeeByUser();
                $headers['branch_id'] = $employee_data != null ? $employee_data->branch_id : 0;
                // dd($employee_data);
            }
            if($headers['branch_id']==0) {
                return "error Cabang";
            }
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
            $headers['is_custom'] = true;
            $headers['customer_name'] = trim($inputs['customer_name']);
            $headers['customer_phone'] = trim($inputs['phone']);
            $headers['others'] =  HelperService::unmaskMoney($inputs['others']);

            $headers['grand_total_item_price'] = $headers['total_item_discount'] = 0;
            $details = [];
            foreach ($custom_details as $custom_detail) {
                $detail = $custom_detail;
                $detail['item_total_price'] = $detail['item_price'] * $detail['item_qty'];
                if($detail['item_discount_fixed_value'] != "") {
                    $detail['item_discount_input'] = $detail['item_discount_fixed_value'];
                    $detail['item_discount_type'] = 2;
                }
                else {
                    $detail['item_discount_fixed_value'] = 0;
                }
                $details[] = $detail;
                $headers['grand_total_item_price'] += $detail['item_total_price'];
                $headers['total_item_discount'] += $detail['item_discount_fixed_value'];
            }

            $total_fix = $headers['grand_total_item_price']+$headers['others']-$headers['total_item_discount'];
            $headers['payment_type'] = HelperService::unmaskMoney($inputs['payment_type']);
            $headers['paid_value'] = HelperService::unmaskMoney($inputs['paid_value_temp']);
            $headers['total_paid'] = HelperService::unmaskMoney($inputs['total_paid']);
            $headers['is_debt'] = $headers['paid_value']<$total_fix;
            if($headers['is_debt']) {
                $headers['debt'] = $total_fix-$headers['paid_value'];
            }
            $headers['change'] = $headers['total_paid']-$headers['paid_value'];

            DB::beginTransaction();
            $transaction_headers = TransactionHeader::create($headers);
            // dd($details);
            foreach ($details as $detail) {
                $detail['item_id'] = 'c';
                $detail['header_id'] = $transaction_headers->id;
                TransactionDetail::create($detail);
            }
            DB::commit();
            session()->put('custom_details',[]);
            return "oke";
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Anda belum menambahkan item, tambahkan terlebih dahulu!',
            'redirect_to' => route('get.custom.cashier')
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
        $pb_discount = [];
        DB::beginTransaction();
        if(!empty(trim($inputs['discount_total_temp']))) {
            $pb_discount['item_id'] = '';
            $pb_discount['qty_item'] = 0;
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

            $pb_discount['profit'] = $pb_discount['turnover'] = 0 - $potongan_total;
            $pb_discount['turnover_description'] = 'Diskon (-) sebesar '.HelperService::maskMoney($potongan_total);

            if(!empty(trim($inputs['discount_voucher_temp']))) {
                $validate_voucher = VoucherService::validateVoucher(trim($inputs['discount_voucher_temp']), true);
                if($validate_voucher['message'] != '') {
                    $pb_discount['turnover_description'] = $pb_discount['turnover_description'].' Menggunakan Voucher '.trim($inputs['discount_voucher_temp']);
                    return response()->json([
                        'status' => 'error',
                        'message' => $validate_voucher['message'],
                        'need_reload' => true
                    ]);
                }
                else {
                    if($validate_voucher['discount_type'] != $headers['discount_total_type'] || $headers['discount_total_input'] != $validate_voucher['discount_value']) {
                        return response()->json([
                            'status' => 'error',
                            'need_reload' =>true,
                            'message' => 'Kesalahan input, halaman akan reload dan coba lagi!'
                        ]);
                    }
                }
            }

            $headers['discount_total_fixed_value'] = $potongan_total;
            if($potongan_total!=intval($inputs['discount_total_fixed_temp'])) {
                //kalo hasil kali server sama client beda, batalkan semua minta input ulang
                return "beda woy";
            }
        }
        $headers['others'] = intval($inputs['others_temp']);

        $total_fix = $headers['grand_total_item_price']+$headers['others']-$headers['total_item_discount']-$headers['discount_total_fixed_value'];
        $headers['payment_type'] = intval($inputs['payment_type_temp']);
        $headers['paid_value'] = intval($inputs['paid_value_temp']);
        $headers['total_paid'] = intval($inputs['total_paid_temp']);
        $headers['is_debt'] = $headers['paid_value']<$total_fix;
        if($headers['is_debt']) {
            $headers['debt'] = $total_fix-$headers['paid_value'];
        }
        $headers['change'] = $headers['total_paid']-$headers['paid_value'];

        if(isset($inputs['member_temp']) && trim($inputs['member_temp']) != '') {
            $headers['member_id'] = trim($inputs['member_temp']);
        }

        $transaction_header = TransactionHeader::create($headers);
        if(count($pb_discount)>0) {
            $pb_discount['header_id'] = $transaction_header->id;
            $pb_discount['branch_id'] = $transaction_header->branch_id;
            $pb_discount['turnover_description'] = 'Koreksi omset invoice no. rekor '.$transaction_header->id.' '.$pb_discount['turnover_description'];
            // $pb_discount['profit'] = 0;
            PembukuanBranch::create($pb_discount);
        }
        $flag_total_item_price = $flag_total_item_discount = 0;
        foreach ($inputs['list_inputs'] as $key => $list_input) {
            $new_detail = explode('|', $list_input);
            if(count($new_detail) != 10) {
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
                // 3 item disc | nullable
                // 4 item disc type | nullable
                // 5 nilai pasti potongan | nullable
                // 6 id pic
                // 7 date jika sewa | nullable
                // 8 branch id tempat ambil jika sewa nullable
                //9 status 1: lgsg diklaim , 2: blm diklaim
            // $input_item = explode('|', $list_input);
            // dd($input_item);
            $details = [];
            $details['header_id'] = $transaction_header->id;
            $details['item_id'] = $new_detail[0];
            $details['item_price'] = intval($new_detail[1]);
            $details['claim_status'] = intval($new_detail[9]);
            $item = Item::with(['jasaIncentive' => function ($query) {
                        $query->orderBy('created_at','desc')
                                ->where('created_at','<',Carbon::now())->first();
                    }])->where('item_id', $details['item_id'])->first();
            // dd($item->jasaIncentive);
            $details['item_qty'] = intval($new_detail[2]);
            $param_pembukuan = [];
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
                    $param_pembukuan['modal_per_produk'] = $branch_stock->modal_per_pcs;
                    $param_pembukuan['qty_produk'] = $details['item_qty'];
                    $branch_stock->save();
                }
            }
            else if($item->item_type == Constant::type_id_jasa){
                if(!empty(trim($new_detail[6]))) {
                    $details['item_pic'] = $new_detail[6];
                }
                $incentive = $item->jasaIncentive;
                $details['pic_incentive'] = $incentive == null ? 0 : $details['item_qty'] * $incentive->incentive;
                $param = [];
                $param['item_id_jasa'] = $item->item_id;
                $param['branch_id'] = $headers['branch_id'];
                $param['qty'] = $param_pembukuan['qty_jasa'] = $details['item_qty'];
                $update_branch_stock = StockService::updateBranchStockByJasa($param, true);

                // dd($update_branch_stock);
                if($update_branch_stock['error_message'] != '') {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaksi Gagal!. Item '.$item->item_name.' akan menyebakan '.$update_branch_stock['error_message'],
                        'need_reload' => true
                    ]);
                }
                else {
                    // dd($update_branch_stock);
                    $param_pembukuan['modal_jasa'] = $update_branch_stock;
                }
            }
            else if($item->item_type == Constant::type_id_sewa) {
                if(!empty(trim($new_detail[8])) && !empty(trim($new_detail[7]))) {
                    $renting_data = [];
                    $renting_data['renting_date'] = HelperService::createDateFromString($new_detail[7]);
                    $renting_data['renting_branch'] = intval($new_detail[8]);
                    $renting_data['item_id'] = $details['item_id'];
                    $param_pembukuan['qty_sewa'] = $renting_data['qty'] = $details['item_qty'];
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
            else if($item->item_type == Constant::type_id_paket) {
                $paket_configurations = PaketConfiguration::where('item_id_paket', $item->item_id)->get();


                if($paket_configurations->count() == 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transaksi Gagal!. Item Paket '.$item->item_name.' belum dikonfigurasi!',
                        'need_reload' => true
                    ]);
                }
                else {
                    $incentive = $item->jasaIncentive;
                    $details['pic_incentive'] = $incentive == null ? 0 : $details['item_qty'] * $incentive->incentive;
                    $param_pembukuan['qty_paket'] = $details['item_qty'];
                    $pb = $param = [];
                    $param['branch_id'] = $headers['branch_id'];

                    foreach($paket_configurations as $paket_configuration) {
                        $param['item_id_jasa'] = $paket_configuration->item_id_jasa;
                        $param['qty'] = $details['item_qty'] * $paket_configuration->qty_jasa;
                        $update_branch_stock = StockService::updateBranchStockByJasa($param, true);

                        // dd($update_branch_stock);
                        if($update_branch_stock['error_message'] != '') {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Transaksi Gagal!. Item '.$item->item_name.' akan menyebakan '.$update_branch_stock['error_message'],
                                'need_reload' => true
                            ]);
                        }
                        else {
                            // dd($update_branch_stock);
                            $param_pembukuan['modal_jasa'][] = $update_branch_stock;
                        }
                    }
                    // $param['qty'] = $details['item_qty'];
                    // $param['paket_configurations'] = $paket_configurations;
                    // $pb = BranchService::pembukuanBranchByPaket($param);
                }

            }
            // dd($param_pembukuan);
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
            $pb = [];
            $pb['header_id'] = $transaction_header->id;
            $pb['detail_id'] = $transaction_detail->id;
            $pb['item_id'] = $transaction_detail->item_id;
            $pb['branch_id'] = $transaction_header->branch_id;
            $pb['description'] = '';
            $pb['modal_total'] = 0;
            if(trim($new_detail[9]) == '1') {
                if($item->item_type == Constant::type_id_produk) {
                    $pb['modal_per_qty_item'] = $param_pembukuan['modal_per_produk'];
                    $pb['qty_item'] = $param_pembukuan['qty_produk'];
                    $pb['modal_total'] = $pb['modal_per_qty_item'] * $pb['qty_item'];
                }
                else if($item->item_type == Constant::type_id_jasa) {
                    $modal_jasa = $param_pembukuan['modal_jasa'];
                    $pb['modal_per_qty_item'] = $modal_jasa['modal_total_per_item'] / $param_pembukuan['qty_jasa'];
                    $pb['qty_item'] = $param_pembukuan['qty_jasa'];
                    foreach($modal_jasa['modal_per_produk'] as $key => $value) {
                        $pb['description'] .= ', '.$key.'/ Qty: '.$modal_jasa['qty_produk'][$key].'/ Modal: '. HelperService::maskMoney(intval($modal_jasa['modal_total'][$key]));
                    }
                    $pb['modal_total'] =  $modal_jasa['modal_total_per_item'];
                    if(!empty(trim($new_detail[6]))) {
                        $pb['modal_total'] = $pb['modal_total'] + $transaction_detail->pic_incentive;
                        $pb['description'] .= ', Insentif Karyawan: '. HelperService::maskMoney(intval($transaction_detail->pic_incentive));
                    }
                }
                else if($item->item_type == Constant::type_id_paket) {
                    foreach ($param_pembukuan['modal_jasa'] as $key => $modal_jasa) {
                        // dd($modal_jasa);
                        $pb['modal_per_qty_item'] = $modal_jasa['modal_total_per_item'] / $param_pembukuan['qty_paket'];
                        $pb['modal_total'] +=  $modal_jasa['modal_total_per_item'];
                        $pb['qty_item'] = $param_pembukuan['qty_paket'];
                        foreach($modal_jasa['modal_per_produk'] as $key => $value) {
                            $pb['description'] .= ', '.$key.'/ Qty: '.$modal_jasa['qty_produk'][$key].'/ Modal: '. HelperService::maskMoney(intval($modal_jasa['modal_total'][$key]));
                        }
                    }
                    if(!empty(trim($new_detail[6]))) {
                        $pb['modal_total'] += + $transaction_detail->pic_incentive;
                        $pb['description'] .= ', Insentif Karyawan: '. HelperService::maskMoney(intval($transaction_detail->pic_incentive));
                    }
                }
                else if($item->item_type == Constant::type_id_sewa) {
                    $pb['qty_item'] = $param_pembukuan['qty_sewa'];
                }
            }
            else {
                $pb['qty_item'] = $details['item_qty'];
            }
            $pb['turnover'] = $transaction_detail->itemTurnover();
            $pb['profit'] = $pb['turnover']-$pb['modal_total'];
            PembukuanBranch::create($pb);
            if(!empty($new_detail[6])) {
                //set omset by employee
                $turnover_input = [
                    'detail_id' => $transaction_detail->id,
                    'employee_id' => $new_detail[6],
                    'turnover'=> $transaction_detail->itemTurnover(),
                    'set_by' => 0
                ];
                $turnover = EmployeeTurnover::create($turnover_input);
                $incentive_input = [
                    'detail_id' => $transaction_detail->id,
                    'employee_id' => $new_detail[6],
                    'incentive'=> $transaction_detail->pic_incentive,
                    'set_by' => 0
                ];
                $incentive = EmployeeIncentive::create($incentive_input);
            }

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
        // return response()->json([
        //     'status' => 'success',
        //     'redirect_to' => '/cashier'
        // ]);
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
        $return = Item::where('for_sale', 1)
                        ->where(function($q){
                            $q->where('item_name', 'like', '%'.request()->term.'%')
                                ->orWhere('item_id', 'like', '%'.request()->term.'%');
                        })
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
                        ->get(['member_id', 'full_name','phone', 'address'])->toArray();

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

    function doClaim()
    {
        $detail_id = intval(Crypt::decryptString(request()->x));
        $detail = TransactionDetail::find($detail_id);

        $employee_data = EmployeeService::getEmployeeByUser();

        $branch_id = $employee_data != null ? $employee_data->branch_id : 0;
        // $branch_id=2;
        if($branch_id == 0) {
            return "Anda bukan kasir!";
        }
        $branch = Branch::find($branch_id);

        if($detail->claim_status==2) {
            //belum diklaim
            $detail->claim_status=1;
            $already_paid = $detail->header->paidValue();
            $already_claim = TransactionDetail::where('header_id', $detail->header_id)
                                            ->where('claim_status', 1);
            $already_claimed = $already_claim->sum('item_total_price') -  $already_claim->sum('item_discount_fixed_value');
            $sisa = $already_paid - $already_claimed;
            // dd($already_paid);
            if($sisa >= $detail->itemTurnover()) {
                // $detail->save();
                DB::beginTransaction();
                $item = Item::where('item_id', $detail->item_id)->first();
                $pb = [];
                $pb['item_id'] = $item->item_id;
                $pb['header_id'] = $detail->header_id;
                $pb['detail_id'] = $detail->id;
                $pb['branch_id'] = $branch->id;
                $pb['turnover'] = 0;
                if($item->item_type == Constant::type_id_produk) {
                    $branch_stock = BranchStock::where('branch_id', $branch->id)
                                                    ->where('item_id', $item->item_id)->first();
                    if($branch_stock) {
                        $branch_stock->stock = $branch_stock->stock-$detail->item_qty;
                        if($branch_stock->stock < 0) {
                            DB::rollBack();
                            return 'Error1. Klaim Gagal. Stok tidak cukup di cabang ini!';
                        }
                        else {
                            $branch_stock->save();

                            $pb['modal_per_qty_item'] = $branch_stock->modal_per_pcs;;
                            $pb['qty_item'] = $detail->item_qty;
                            $pb['modal_total'] = $pb['modal_per_qty_item'] * $pb['qty_item'];
                        }
                    }
                    else {
                        DB::rollBack();
                        return 'Error2. Klaim Gagal. Stok tidak cukup di cabang ini!';
                    }
                }
                else if($item->item_type == Constant::type_id_jasa) {
                    $param = [];
                    $param['item_id_jasa'] = $item->item_id;
                    $param['branch_id'] = $branch->id;
                    $param['qty'] = $detail->item_qty;
                    $update_branch_stock = StockService::updateBranchStockByJasa($param, true);

                    // dd($update_branch_stock);
                    if($update_branch_stock['error_message'] != '') {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Transaksi Gagal!. Item '.$item->item_name.' akan menyebakan '.$update_branch_stock['error_message'],
                            'need_reload' => true
                        ]);
                    }
                    else {
                        // dd($update_branch_stock);
                        $param_pembukuan['modal_jasa'] = $update_branch_stock;
                    }

                    $modal_jasa = $param_pembukuan['modal_jasa'];
                    $pb['modal_per_qty_item'] = $modal_jasa['modal_total_per_item'] / $detail->item_qty;
                    $pb['qty_item'] = $detail->item_qty;
                    $pb['description'] = '';
                    foreach($modal_jasa['modal_per_produk'] as $key => $value) {
                        $pb['description'] .= ', '.$key.'/ Qty: '.$modal_jasa['qty_produk'][$key].'/ Modal: '. HelperService::maskMoney(intval($modal_jasa['modal_total'][$key]));
                    }
                    $pb['modal_total'] =  $modal_jasa['modal_total_per_item'];
                    if($detail->item_pic) {
                        $pb['modal_total'] =  $pb['modal_total'] + $detail->pic_incentive;
                        $pb['description'] .= ', Insentif Karyawan: '. HelperService::maskMoney(intval($transaction_detail->pic_incentive));
                    }
                }
                else if($item->item_type == Constant::type_id_paket) {
                    foreach ($param_pembukuan['modal_jasa'] as $key => $modal_jasa) {
                        // dd($modal_jasa);
                        $pb['modal_per_qty_item'] = $modal_jasa['modal_total_per_item'] / $param_pembukuan['qty_paket'];
                        $pb['modal_total'] +=  $modal_jasa['modal_total_per_item'];
                        $pb['qty_item'] = $param_pembukuan['qty_paket'];
                        foreach($modal_jasa['modal_per_produk'] as $key => $value) {
                            $pb['description'] .= ', '.$key.'/ Qty: '.$modal_jasa['qty_produk'][$key].'/ Modal: '. HelperService::maskMoney(intval($modal_jasa['modal_total'][$key]));
                        }
                    }
                    if(!empty(trim($new_detail[6]))) {
                        $pb['modal_total'] += + $transaction_detail->pic_incentive;
                        $pb['description'] .= ', Insentif Karyawan: '. HelperService::maskMoney(intval($transaction_detail->pic_incentive));
                    }
                }
                else if($item->item_type == Constant::type_id_sewa) {
                    $pb['qty_item'] = $param_pembukuan['qty_sewa'];
                }

                $pb['profit'] = $pb['turnover']-$pb['modal_total'];
                PembukuanBranch::create($pb);
                $detail->save();
                DB::commit();
                return "oke";
                return redirect()->route('get.invoice.cashier',['param'=>request()->back, 'detail_klaim'=>1]);
            }
            else {
                return "klaim gagal karena pembayaran belum lunas. <a href='".route('get.invoice.cashier',['param'=>request()->back, 'detail_klaim'=>1])."'>Kembali</a>";
            }
            return "oke";
        }
        //kalo udah notfound
        abort(404);
    }
}
