<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionHeader;
use App\Branch;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    function trans()
    {
        $data = [];

        $status = array (
                1 => 'Sedang berjalan',
                2 => 'Selesai',
                3 => 'Dibatalkan'
            );
        $data['count'][1] = 0;
        $data['count'][2] = 0;
        $data['count'][3] = 0;
        $data['branch'] = [];
        $headers = TransactionHeader::whereDate('created_at',Carbon::today()->toDateString())->get();
        foreach ($headers as $key => $header) {
            $data['count'][$header->status] = $data['count'][$header->status] + 1;
            if(!isset($data['branch'][$header->branch_id])) {
                $data['branch'][$header->branch_id]['name'] = Branch::find($header->branch_id)->branch_name;
                $data['branch'][$header->branch_id]['count'][$header->status] = 1;
            }
            else {
                $data['branch'][$header->branch_id]['count'][$header->status] = $data['branch'][$header->branch_id]['count'][$header->status] + 1;
            }
        }
        dd($data);
    }
}
