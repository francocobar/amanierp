<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Constant;
use Carbon\Carbon;
use App\TransactionHeader;
use App\TransactionDetail;
use App\NextPayment;
use App\Branch;
use HelperService;
use DB;

class Report2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager');
    }

    function getSalesReport($period, $spesific='0', $branch= '0')
    {
        $data = [];
        if(!session()->has('branch_id')) {
            $data['branches'] = Branch::all();
        }
        else if(session()->has('branch_id') && session('branch_id') != intval($branch)) {
            abort(404);
        }
        $data['branch_id'] = $branch;
        if($branch=='0') {
            $branch_selected = 'Semua Cabang';
        }
        else {
            $obj_branch = Branch::find(intval($branch));
            if($obj_branch) {
                $branch_selected = 'Cabang '.$obj_branch->branch_name;
            }
            else {
                abort(404);
            }
        }
        if($period == Constant::daily_period) {
            if($spesific=='0') {
                $spesific = Carbon::today()->toDateString();
            }
            $spesifics = explode('-', $spesific);
            // dd($spesifics);
            if(count($spesifics) != 3) {
                abort(404);
            }
            else {
                $data['date_period'] = $spesifics[2].'-'.$spesifics[1].'-'.$spesifics[0];
            }

            $headers =  TransactionHeader::whereDate('created_at', $spesific)
                            ->whereIn('status', [2, 4]);
            $installments = NextPayment::whereDate('created_at', $spesific);
            $data['title'] = 'Laporan '.$branch_selected.' tanggal '.HelperService::inaDate($spesific);
        }
        else {
            if($spesific=='0') {
                $year = date('Y');
                $month = date('m');
            }
            else {
                $spesifics = explode('-', $spesific);
                if(count($spesifics) != 2) {
                    abort(404);
                }
                else {
                    $year = intval($spesifics[0]);
                    $month = intval($spesifics[1]);
                }
            }

            $headers =  TransactionHeader::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->whereIn('status', [2, 4]);
            $installments = NextPayment::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month);
            $data['title'] = 'Laporan '.$branch_selected.' bulan '.HelperService::monthName($month).' '.$year;
            $data['year'] = $year; $data['month'] = $month;
        }

        if(intval($branch) != 0) {
            $headers = $headers->where('branch_id', intval($branch));
            $installments = $installments->where('branch_id', intval($branch));
        }
        $installments_temp  = $installments->get();
        $ids = [];
        foreach ($installments_temp as $key => $value) {
            if($value->header->status==2) {
                $ids[] = $value->header_id;
            }
        }
        $installments = $installments->whereIn('header_id', $ids)->get();
        $headers = $headers->get();
        $data['headers'] = $headers;
        // foreach ($data['headers'] as $key => $value) {
        //     echo $value->branch_id.",";
        // }
        // return "";
        $data['installments'] = $installments;
        $data['transaction_total'] = $headers->sum('grand_total_item_price')
                                -$headers->sum('discount_total_fixed_value')
                                -$headers->sum('total_item_discount')
                                +$headers->sum('others');
        $data['transaction_turnover'] = $headers->sum('paid_value')+$installments->sum('paid_value');
        $data['transaction_debt'] = $headers->where('status',2)->sum('debt');
        return view('report2.trans-report', $data);
    }

    function topMembers($spesific='0', $branch= '0')
    {
        $data = [];
        if(!session()->has('branch_id')) {
            $data['branches'] = Branch::all();
        }
        else if(session()->has('branch_id') && session('branch_id') != intval($branch)) {
            abort(404);
        }
        $data['branch_id'] = $branch;
        if($branch=='0') {
            $branch_selected = 'Semua Cabang';
        }
        else {
            $obj_branch = Branch::find(intval($branch));
            if($obj_branch) {
                $branch_selected = 'Cabang '.$obj_branch->branch_name;
            }
            else {
                abort(404);
            }
        }
        if($spesific=='0') {
            $year = date('Y');
            $month = date('m')-1;
        }
        else {
            $spesifics = explode('-', $spesific);
            if(count($spesifics) != 2) {
                abort(404);
            }
            else {
                $year = intval($spesifics[0]);
                $month = intval($spesifics[1]);
            }
        }

        $data['title'] = 'Top 30 Members berdasarkan Transaksi '.$branch_selected.' bulan '.HelperService::monthName($month).' '.$year;
        $data['year'] = $year;
        $data['month'] =  $month;

        $where_time = ' and month(created_at)='.$month.' and year(created_at)='.$year;
        $where_time_2 = ' and month(next_payments.created_at)='.$month.' and year(next_payments.created_at)='.$year;
        if(intval($branch) != 0) {
            $where_time .= ' and branch_id='.intval($branch);
            $where_time_2 .= ' and next_payments.branch_id='.intval($branch);
        }
        $data['top_members'] = DB::select('select member_id, sum(paid_value) as total_trans from
        (
        select member_id,paid_value  from transaction_headers
        where status=2 and member_id is not null '.$where_time.'
        union all
        select member_id, next_payments.paid_value from next_payments
        left join transaction_headers
        on transaction_headers.id = next_payments.header_id
        where member_id is not null '.$where_time_2.'
        ) as a
        group by member_id order by total_trans desc
        LIMIT 30');
        return view('report2.top-members-report', $data);
    }

    function topItems($branch='0', $from='0', $to= '0')
    {
        $data = [];
        if($from=='0' || $to=='0') {
            abort(404);
        }

        $froms = explode('-', $from);
        // dd($spesifics);
        if(count($froms) != 3) {
            abort(404);
        }
        else {
            $data['from'] = $froms[2].'-'.$froms[1].'-'.$froms[0];
        }
        $tos = explode('-', $to);
        // dd($spesifics);
        if(count($tos) != 3) {
            abort(404);
        }
        else {
            $data['to'] = $tos[2].'-'.$tos[1].'-'.$tos[0];
        }
        if(!session()->has('branch_id')) {
            $data['branches'] = Branch::all();
        }
        else if(session()->has('branch_id') && session('branch_id') != intval($branch)) {
            abort(404);
        }
        $data['branch_id'] = $branch;
        if($branch=='0') {
            $branch_selected = 'Semua Cabang';
        }
        else {
            $obj_branch = Branch::find(intval($branch));
            if($obj_branch) {
                $branch_selected = 'Cabang '.$obj_branch->branch_name;
            }
            else {
                abort(404);
            }
        }
        $data['title'] = 'Top 30 Items berdasarkan Qty Transaksi '.$branch_selected.' Tanggal '. HelperService::inaDate($from);
        if($from != $to) {
            $data['title']  .= ' - '. HelperService::inaDate($to);
        }
        $item_ids = '';
        if(intval($branch) != 0) {
            $item_ids = " and header_id in (select id from transaction_headers
            where status=2 and branch_id=".intval($branch)." and DATE(created_at) >= '".$from."'
            and DATE(created_at) <= '".$to."')";
            // dd($item_ids);
        }
        $data['top_items'] = DB::select("select item_id, sum(item_qty_done) as qty from transaction_details
            where item_id like 'I%'
            and item_qty_done>0
            and DATE(created_at) >= '".$from."'
            and DATE(created_at) <= '".$to."'
            ".$item_ids."
            group by item_id
            order by qty desc LIMIT 30");
        return view('report2.top-items-report', $data);
    }

    function salesDetails($branch='0', $from='0', $to= '0')
    {
        $data = [];
        if($from=='0' || $to=='0') {
            abort(404);
        }

        $froms = explode('-', $from);
        // dd($spesifics);
        if(count($froms) != 3) {
            abort(404);
        }
        else {
            $data['from'] = $froms[2].'-'.$froms[1].'-'.$froms[0];
        }
        $tos = explode('-', $to);
        // dd($spesifics);
        if(count($tos) != 3) {
            abort(404);
        }
        else {
            $data['to'] = $tos[2].'-'.$tos[1].'-'.$tos[0];
        }
        if(!session()->has('branch_id')) {
            $data['branches'] = Branch::all();
        }
        else if(session()->has('branch_id') && session('branch_id') != intval($branch)) {
            abort(404);
        }
        $data['branch_id'] = $branch;
        if($branch=='0') {
            $branch_selected = 'Semua Cabang';
        }
        else {
            $obj_branch = Branch::find(intval($branch));
            if($obj_branch) {
                $branch_selected = 'Cabang '.$obj_branch->branch_name;
            }
            else {
                abort(404);
            }
        }
        $data['title'] = 'Rincian Penjualan '.$branch_selected.' Tanggal '. HelperService::inaDate($from);
        if($from != $to) {
            $data['title']  .= ' - '. HelperService::inaDate($to);
        }
        $item_ids = '';
        if(intval($branch) != 0) {
            $item_ids = " and header_id in (select id from transaction_headers
            where status=2 and branch_id=".intval($branch)." and DATE(created_at) >= '".$from."'
            and DATE(created_at) <= '".$to."')";
            // dd($item_ids);
        }
        $data['top_items'] = DB::select("select item_id, sum(item_qty_done) as qty from transaction_details
            where item_id like 'I%'
            and item_qty_done>0
            and DATE(created_at) >= '".$from."'
            and DATE(created_at) <= '".$to."'
            ".$item_ids."
            group by item_id
            order by qty desc");
        return view('report2.sales-details-report', $data);
    }

    function getSsalesdetailsByAjax(Request $request)
    {
        $inputs = $request->all();
        $data = [];
        $froms = explode('-', $inputs['from']);
        // dd($spesifics);
        if(count($froms) != 3) {
            abort(404);
        }
        else {
            $from = $froms[2].'-'.$froms[1].'-'.$froms[0];
        }
        $tos = explode('-', $inputs['to']);
        // dd($spesifics);
        if(count($tos) != 3) {
            abort(404);
        }
        else {
            $to = $tos[2].'-'.$tos[1].'-'.$tos[0];
        }


        $total_trans = TransactionDetail::whereDate('created_at','>=', $from)
                                               ->whereDate('created_at','<=',$to)
                                               ->whereIn('item_id', $inputs['item_ids'])
                                               ->where('claim_status', 1);
        $costumize_items = TransactionDetail::whereDate('created_at','>=', $from)
                                               ->whereDate('created_at','<=',$to)
                                               ->whereNotIn('item_id', $inputs['item_ids'])
                                               ->where('claim_status', 1);
        $discount = TransactionHeader::whereDate('created_at','>=', $from)
                                               ->whereDate('created_at','<=',$to)
                                               ->where('status', 2);
        if($inputs['branch_id'] != '0')
        {
            $header_ids = TransactionHeader::whereDate('created_at','>=', $from)
                                                   ->whereDate('created_at','<=',$to)
                                                   ->where('branch_id', intval($inputs['branch_id']))
                                                   ->where('status', 2)
                                                   ->get(['id'])->pluck('id')->toArray();
            $total_trans = $total_trans->whereIn('header_id', $header_ids);
            $discount = $discount->whereIn('id', $header_ids);
            $costumize_items = $costumize_items->whereIn('header_id', $header_ids);

        }

        $data['total_trans'] = $total_trans->select(DB::raw('sum(item_total_price) as item_total_prices, item_id'))
                                    ->groupBy('item_id')->get();
        $costumize_items  =  $costumize_items->get();
        $data['costumize_items'] = $costumize_items->sum('item_total_price');
        $discount = $discount->get();
        $data['discount'] = $discount->sum('discount_total_fixed_value') + $discount->sum('total_item_discount');
        return $data;

    }
}
