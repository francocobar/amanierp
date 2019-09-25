<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Model\Cashier\HeaderOngoing;
use App\TransactionDetail;
use App\Branch;
use App\Member;
use UserService;
use EmployeeService;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{
    protected $error_message = '';
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }


    protected function index(Branch $branch)
    {
        if(!UserService::isSuperadmin())
        {
            $obj_my_employee = EmployeeService::getEmployeeByUser();
            if($obj_my_employee == null || $obj_my_employee->branch_id != $branch->id)
            {
                abort(404);
            }
        }

        $flag = array (
            'branch' => $branch->id,
            'user'   => Sentinel::getUser()->id
        );
        $data['flag_encrypted'] = encrypt($flag);
        $data['title'] = 'Cashier';
        $data['headline'] = 'Cashier';

        return view('cashier.rev.index-per-branch', $data);
    }

    //transaksi2 sedang berjalan percabang
    protected function getOngoingTransactions(Branch $branch)
    {
        $inputs = request()->all();
        if(!UserService::isSuperadmin())
        {
            if($branch->id != $flag['branch']) {
                return response(array('error'=>'401 Unauthorized'), 401);
            }
            $obj_my_employee = EmployeeService::getEmployeeByUser();
            if($obj_my_employee == null || $obj_my_employee->branch_id != $branch->id)
            {
                return response(array('error'=>'401 Unauthorized'), 401);
            }
        }
        return array('data'=>HeaderOngoing::where('branch_id', $branch->id)->where('already_finish', false)->get());
    }

    private function createHeaderOngoing($param)
    {
        $header_ongoing = new HeaderOngoing();
        if($param['type_trans'] == "1" && isset($param['member']) && !empty($param['member'])) {
            $header_ongoing->member_id = $param['member'];
            $member = Member::where('member_id', trim($header_ongoing->member_id))->first();
            if($member != null) {
                $header_ongoing->customer_name = $member->full_name;
            }
            else {
                dd('terjadi kesalahan');
            }
        }
        else if($param['type_trans'] == "2" && isset($param['guest_name']) && !empty($param['guest_name'])) {
            $header_ongoing->customer_name = $param['guest_name'];
        }
        else {
            dd('terjadi kesalahan');
        }
        $header_ongoing->item_price_sub_total = 0;
        $header_ongoing->item_discount_sub_total = 0;
        $header_ongoing->sub_total = 0;
        $header_ongoing->discount2 = 0;
        $header_ongoing->total = 0;

        $header_ongoing->cashier_user_id = Sentinel::getUser()->id;
        $header_ongoing->branch_id = $param['branch_id'];
        $header_ongoing->save();
        return $header_ongoing;
    }

    protected function createTransaction($type_trans, Branch $branch)
    {
        $inputs = request()->all();
        $flag = decrypt($inputs['flencry']);
        if(!UserService::isSuperadmin())
        {
            if($branch->id != $flag['branch']) {
                return response(array('error'=>'401 Unauthorized'), 401);
            }
            $obj_my_employee = EmployeeService::getEmployeeByUser();
            if($obj_my_employee == null || $obj_my_employee->branch_id != $branch->id)
            {
                return response(array('error'=>'401 Unauthorized'), 401);
            }
        }
        $inputs['branch_id'] = $branch->id;
        $inputs['type_trans'] = $type_trans;
        $header_ongoing = $this->createHeaderOngoing($inputs);
        if($header_ongoing) {
            return array('data'=>$header_ongoing);
        }
        return array('data'=>null);
    }
}
