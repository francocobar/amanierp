<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionHeader;
use App\TransactionDetail;
use Constant;
use DB;
use Carbon\Carbon;
use HelperService;
use App\Branch;


class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager');
    }

    function getSalesReport($period, $spesific='0', $branch= '0')
    {
        $header_sum =  $header_pelunasan_hutang = $headers = $details = null;
        $report = [];
        if(session()->has('branch_id') && session('branch_id') != intval($branch)) {
            abort(404);
        }
        $report['branch'] = $branch;
        if($report['branch'] != 0) {
            $report['obj_branch'] = Branch::find($branch);
            if($report['obj_branch'] == null) abort(404);
        }

        $selects = array(
            'sum(grand_total_item_price) AS sum_total_item_price',
            'SUM(total_paid) AS sum_total_paid',
            'sum(transaction_headers.change) AS sum_change',
            'sum(transaction_headers.debt) AS sum_debt',
            'sum(transaction_headers.total_item_discount) AS sum_item_discount',
            'sum(transaction_headers.discount_total_fixed_value) AS sum_discount_total',
            'sum(transaction_headers.others) AS sum_others',
        );

        $selects_pelunasan_hutang = array(
            'sum(total_paid2) AS sum_total_paid2',
            'sum(transaction_headers.change2) AS sum_change2',
        );
        $header_sum = $headers = $header_pelunasan_hutang = null;
        if($period == Constant::daily_period) {
            if($spesific=='0') {
                $spesific = Carbon::today()->toDateString();
                $spesifics = Carbon::today();
                $report['date_period'] = sprintf("%02d", $spesifics->day).'-'.sprintf("%02d", $spesifics->month).'-'.$spesifics->year;
            }
            else {
                $spesifics = explode('-', $spesific);
                // dd($spesifics);
                if(count($spesifics) != 3) {
                    abort(404);
                }
                else {
                    $report['date_period'] = $spesifics[2].'-'.$spesifics[1].'-'.$spesifics[0];
                }
                // dd($report);
            }
            $report['period'] = HelperService::inaDate($spesific);
            $header_sum =  TransactionHeader::whereDate('created_at', $spesific);
            $headers =  TransactionHeader::whereDate('created_at', $spesific);
            $header_pelunasan_hutang = TransactionHeader::whereDate('payment2_date', $spesific)
                        ->selectRaw(implode(',', $selects_pelunasan_hutang));
        }
        else if($period == Constant::monthly_period) {
            $report['year'] = date('Y');
            $report['month'] = date('m');
            if($spesific!='0') {
                $spesifics = explode('-', $spesific);
                if(count($spesifics) != 2) {
                    abort(404);
                }
                else {
                    $report['year'] = intval($spesifics[0]);
                    $report['month'] = intval($spesifics[1]);
                }
            }

            $report['period'] = HelperService::monthName($report['month']).' '.$report['year'];
            $header_sum =  TransactionHeader::whereMonth('created_at', '=', $report['month'])
                        ->whereYear('created_at', '=', $report['year']);
            $headers =  TransactionHeader::whereMonth('created_at', '=', $report['month'])
                        ->whereYear('created_at', '=', $report['year']);
            $header_pelunasan_hutang = TransactionHeader::whereMonth('created_at', '=', $report['month'])
                        ->whereYear('created_at', '=', $report['year']);
        }

        if($branch != '0') {
            $header_sum = $header_sum->where('branch_id', intval($branch));
            $headers = $headers->where('branch_id', intval($branch));
            $header_pelunasan_hutang = $header_pelunasan_hutang->where('branch_id', intval($branch));
        }

        $header_sum = $header_sum->selectRaw(implode(',', $selects))
                        ->get();
        // dd($header_sum);
        $list_id_header = $headers->get(['id'])->pluck('id')->toArray();
        // dd($list_id_header);
        $headers = $headers->get();
        $header_pelunasan_hutang = $header_pelunasan_hutang->selectRaw(implode(',', $selects_pelunasan_hutang))->get();
        $selects_details = array(
            'item_id',
            'SUM(item_qty) AS item_qty_',
            'sum(item_total_price) AS item_total_price_',
        );
        // $details = TransactionDetail::with('itemInfo')->whereIn('header_id', $list_id_header)->get();

        $details = TransactionDetail::with('itemInfo')->whereIn('header_id', $list_id_header)->groupBy('item_id')
                ->selectRaw(implode(',', $selects_details))->get();

        $report['omset'] = $header_sum[0]['sum_total_paid']-$header_sum[0]['sum_change']
                            +$header_pelunasan_hutang[0]['sum_total_paid2']-$header_pelunasan_hutang[0]['sum_change2'];
        $report['potongan'] = $header_sum[0]['sum_item_discount']+$header_sum[0]['sum_discount_total'];
        $report['others'] = $header_sum[0]['sum_others'];
        $report['total_jual'] = $header_sum[0]['sum_total_item_price'] - $report['potongan'] + $header_sum[0]['sum_others'];
        $report['tunai'] = $header_sum[0]['sum_total_paid']-$header_sum[0]['sum_change'];
        $report['tunai_piutang'] = $header_pelunasan_hutang[0]['sum_total_paid2']-$header_pelunasan_hutang[0]['sum_change2'];
        $report['non_tunai'] = $header_sum[0]['sum_debt'] == null ? 0 :  $header_sum[0]['sum_debt'];
        // dd($details);
        return view('report.sales-report',[
            'report' => $report,
            'details' => $details,
            'headers' => $headers,
            'branches' => Branch::get()
        ]);
        dd($report);
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

        return view('report.search-invoice',[
            'headers' => $headers,
            'keyword' => trim(request()->invoice)
        ]);
    }
}
