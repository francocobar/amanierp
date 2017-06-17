<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionHeader;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\RentingData;
use Sentinel;
use EmployeeService;
use UserService;

class RentingController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }

    function rentingDatas($header_id)
    {
        $header = TransactionHeader::find(intval($header_id));

        if($header!=null && $header->rentingDatas->count()>0) {
            return view('report.renting-data',[
                'header' => $header,
                'today' => Carbon::now(),
                'employee' => EmployeeService::getEmployeeByUser(),
                'is_superadmin' => UserService::isSuperadmin()
            ]);
        }
        abort(404);
    }

    function rentingDatasByTime()
    {
        $renting_datas = null;
        $var = [];
        if(request()->s=='1') {
            $var['start_date']= request()->start_date ? request()->start_date : Carbon::today()->toDateString();
            // dd(request()->start_date );
            $renting_datas = RentingData::where('renting_date','>=',$var['start_date'])
                                ->orderBy('renting_date');
        }
        else if(request()->s=='3') {
            //yg sudah dikembalikan
            $var['start_date']= request()->start_date ? request()->start_date : Carbon::today()->toDateString();
            // dd(request()->start_date );
            $renting_datas = RentingData::where('renting_date','<=',$var['start_date'])
                                ->orderBy('renting_date');
        }
        else if(request()->s=='2'){
            $var['start_date']= Carbon::today()->toDateString();
            $renting_datas = RentingData::where('renting_date','<=',$var['start_date'])
                                ->orderBy('renting_date','desc');
        }
        else if(request()->s=='4'){
            $var['start_date']= Carbon::today()->addDay(-3)->toDateString();
            $renting_datas = RentingData::where('renting_date','<=',$var['start_date'])
                                ->orderBy('renting_date');
        }
        else if(request()->s=='5'){
            //yg udah lewat
            $var['start_date']= Carbon::today()->toDateString();
            $renting_datas = RentingData::where('renting_date','<',$var['start_date'])
                                ->orderBy('renting_date','desc');
        }
        $spesifics = explode('-', $var['start_date']);
        // dd($spesifics);
        if(count($spesifics) != 3) {
            abort(404);
        }
        else {
            $var['date_view'] = $spesifics[2].'-'.$spesifics[1].'-'.$spesifics[0];
        }


        if(request()->s=='1') {
            //semua status
            $renting_datas = $renting_datas->get();
        }
        else if(request()->s=='2' || request()->s=='4') {
            //sudah diambil, belum dikembalikan : 2
            //lebih dari 3 hari belum dikembalikan :  4
            $renting_datas = $renting_datas->whereNotNull('taking_date')
                                ->whereNull('return_date')
                                ->get();
        }
        else if(request()->s=='3') {
            //sudah diambil, sudah dikembalikan : 3

            $renting_datas = $renting_datas->whereNotNull('taking_date')
                                ->whereNotNull('return_date')
                                ->get();
        }
        else if(request()->s=='5') {
            $renting_datas = $renting_datas->get();
        }

        return view('cashier.renting-data-by-time',[
            'renting_datas' => $renting_datas,
            'is_superadmin' => UserService::isSuperadmin(),
            'var' => $var
        ]);
    }

    function changeStatusRentingData($action, $reting_data_id)
    {
        if(request()->headers->get('referer')) {
            $renting_data_id = Crypt::decryptString($reting_data_id);
            $data = RentingData::find(intval($renting_data_id));
            if($action=='taking') {
                if($data->taking_date)
                    abort(404);

                $data->taking_date = Carbon::now();
                $data->taking_pic = Sentinel::getUser()->id;
                $data->save();
                return redirect(request()->headers->get('referer'));
            }
            else if($action =='return') {
                if($data->taking_date==null || $data->return_date!=null)
                    abort(404);

                $data->return_date = Carbon::now();
                $data->return_pic = Sentinel::getUser()->id;
                $data->save();
                return redirect(request()->headers->get('referer'));
            }
        }
        abort(404);

    }
}
