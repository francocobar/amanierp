<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Model\Cashier\HeaderOngoing;
use App\Model\Transaction\TrxHeader;
use App\Model\Payment\Payment;
use App\TransactionDetail;
use App\Branch;
use App\Member;
use App\Helper\CouponHelper;
use UserService;
use EmployeeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeriesController extends Controller
{
    protected $error_message = '';
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }

    protected function seriesOngoingTrans($branch, $header)
    {

        if($header->already_finish == 0 && $header->branch_id == $branch->id && ($header->cashier_user_id == Sentinel::getUser()->id || UserService::isSuperadmin())) {
            $data = [];
            $data['title'] = 'Cashier - Transaksi Paket Series';
            $data['headline'] = 'Cashier - Transaksi Paket Series';
            $header->is_series = true;
            $header->save();
            $data['header'] = $header;
            $items = Cache::get('list_item_ongoing'.$header->id, []);
            if(count($items)>1) {
                Cache::put('list_item_ongoing'.$header->id, [], 1440);
                $items = [];
            }
            $data['items'] = $items;
            $flag = array (
                'branch' => $branch->id,
                'user'   => Sentinel::getUser()->id
            );
            $data['flag_encrypted'] = encrypt($flag);
            return view('cashier.rev.payment-ongoing-series-trans', $data);
        }
        abort(404);
    }
}
