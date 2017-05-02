<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionHeader;

class RentingController extends Controller
{
    function rentingDatas($header_id)
    {
        $header = TransactionHeader::find(intval($header_id));

        if($header!=null && $header->rentingDatas->count()>0) {
            return view('report.renting-data',[
                'header' => $header
            ]);
        }
        abort(404);
    }
}
