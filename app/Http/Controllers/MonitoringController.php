<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionHeader;
use App\Branch;
use Carbon\Carbon;
use HelperService;

class MonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager');
    }

    function trans(Request $request)
    {
        $data = [];
        if(!$request->ajax()){

            $data['title'] = 'Monitoring - '.HelperService::inaDate(Carbon::today()->toDateString());
            $data['fa_icon'] = 'fa-line-chart';
            return view('monitoring.trans', $data);
        }
        $status = array (
                1 => 'Sedang berjalan',
                2 => 'Selesai',
                3 => 'Dibatalkan'
            );
        $data['count'][1] = 0;
        $data['count'][2] = 0;
        $data['count'][3] = 0;
        $headers = TransactionHeader::whereDate('created_at',Carbon::today()->toDateString())->get();
        foreach ($headers as $key => $header) {
            $data['count'][$header->status]++;
            if(!isset($data['per']['branch'.$header->branch_id][$header->status])) {
                foreach ($status as $key_st => $st) {
                    $data['per']['branch'.$header->branch_id][$key_st] = 0;
                }
                $data['per']['branch'.$header->branch_id][$header->status] = 1;
                $data['branches'][] = Branch::find($header->branch_id);
            }
            else {
                $data['per']['branch'.$header->branch_id][$header->status]++;
            }
        }
        return $data;
    }
}
