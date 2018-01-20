<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Constant;
use Carbon\Carbon;
use App\TransactionHeader;
use App\NextPayment;
use App\Branch;
use HelperService;

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
                            ->where('status', 2);
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
                            ->where('status', 2);
            $installments = NextPayment::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month);
            $data['title'] = 'Laporan '.$branch_selected.' bulan '.HelperService::monthName($month).' '.$year;
            $data['year'] = $year; $data['month'] = $month;
        }

        if(intval($branch) != 0) {
            $headers = $headers->where('branch_id', intval($branch));
            $installments = $installments->where('branch_id', intval($branch));
        }
        $headers = $headers->get();
        $installments  = $installments->get();
        $data['headers'] = $headers;
        $data['installments'] = $installments;
        $data['transaction_total'] = $headers->sum('grand_total_item_price')
                                -$headers->sum('discount_total_fixed_value')
                                -$headers->sum('total_item_discount')
                                +$headers->sum('others');
        $data['transaction_turnover'] = $headers->sum('paid_value')+$installments->sum('paid_value');
        $data['transaction_debt'] = $headers->sum('debt');
        return view('report2.trans-report', $data);
    }
}
