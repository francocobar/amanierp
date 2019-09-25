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

class DepositController extends Controller
{
    protected $error_message = '';
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }

    protected function depositOngoingTrans($branch, $header)
    {

        if($header->already_finish == 0 && $header->branch_id == $branch->id && ($header->cashier_user_id == Sentinel::getUser()->id || UserService::isSuperadmin())) {
            $data = [];
            $data['title'] = 'Cashier - Deposit';
            $data['headline'] = 'Cashier - Deposit';
            return view('cashier.rev.deposit-ongoing-trans', $data);
        }
        abort(404);
    }
}
