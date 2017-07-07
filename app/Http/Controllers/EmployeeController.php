<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use UserService;
use HelperService;
use EmployeeService;
use App\User;
use App\Employee;
use App\EmployeeSalary;
use Sentinel;
use Carbon\Carbon;
use App\TransactionDetail;
use App\EmployeeIncentive;
use Illuminate\Support\Facades\DB;
use App\PembukuanBranch;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager')->except('mySalary','giveNewPassword');

        // $this->middleware('log')->only('index');
        //
        // $this->middleware('subscribed')->except('store');
    }

    function addEmployeeDo(Request $request)
    {
        $inputs = $request->all();
        $inputs['full_name'] = ucwords(trim(strtolower($inputs['full_name'])));
        $inputs['email'] = trim($inputs['email']);
        $inputs['address'] = trim($inputs['address']);
        $inputs['phone'] = trim($inputs['phone']);

        if(trim($inputs['branch']) == 'add_by_manager') {
            $employee_data = EmployeeService::getEmployeeByUser();
            // dd($employee_data);
            $inputs['branch_id'] = $employee_data->branch_id;
        }
        else {
            try {
                $inputs['branch_id'] = Crypt::decryptString($inputs['branch']);
            } catch (DecryptException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error. Halaman akan reload dan harap coba lagi!',
                    'need_reload' => true
                ]);
            }
        }
        // dd($inputs);
        // dd(HelperService::getPrefixEmployeeId($inputs['branch_id']));

        $inputs_user = [];
        //SET FIRST NAME AND LAST NAME
        if(strpos($inputs['full_name'], ' ') !== false ) {
            $index_space = strpos($inputs['full_name'], ' ');
            $inputs_user['first_name'] = substr($inputs['full_name'], 0, $index_space);
            $inputs_user['last_name'] = substr($inputs['full_name'], $index_space, strlen($inputs['full_name'])-$index_space);
            $inputs_user['last_name'] = trim($inputs_user['last_name']);
        }
        else {
            $inputs_user['first_name'] = $inputs_user['last_name'] = $inputs['full_name'];
        }

        //SET EMAIL/Username
        if(!isset($inputs['email']) || empty($inputs['email'])) {
            $inputs_user['email'] = strtolower(substr($inputs_user['first_name'].$inputs_user['last_name'],0,6));
            $i=1;
            $temp = $inputs_user['email'] . $i;
            ;
            while(User::where('email',$temp)->first()!=null) {
                $i++;
                $temp = $inputs_user['email'] . $i;
            }
            $inputs_user['email'] = $temp;
        }
        else {
            if(User::where('email',$inputs['email'])->first()!=null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email/Username sudah terdaftar!',
                    // 'need_reload' => true
                ]);
            }
            $inputs_user['email'] = $inputs['email'];
        }

        //SET PASSWORD
        $inputs_user['password'] = substr(str_replace(' ','$',$inputs['full_name']), 0, 3);
        $random = rand(13579, 98642);
        $inputs_user['password'] .= $random;

        $valid_since =
        $new_user = UserService::createUser($inputs_user);
        $role = Sentinel::getRoleRepository()->findBySlug('staff');
        $role->users()->attach($new_user);
        //create employee data
        $inputs['user_id'] = $new_user->id;
        $prefix_employee_id = HelperService::getPrefixEmployeeId($inputs['branch_id']);

        $number_id = 1;

        $last_employee = Employee::where('branch_id', $inputs['branch_id'])
                                    ->where('employee_id','like',$prefix_employee_id.'%')
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        if($last_employee != null) {
            $last_number_id = str_replace($prefix_employee_id,'',$last_employee->employee_id);
            $number_id = $last_number_id+1;
        }

        $inputs['employee_id'] = $prefix_employee_id.sprintf("%04d", $number_id);
        // dd($dob);
        $inputs['dob'] = HelperService::createDateFromString(trim($inputs['dob']));
        unset($inputs['_token']);
        unset($inputs['branch']);

        $inputs['employee_salary'] = str_replace('.', '', $inputs['salary']);
        if($inputs['salary_since'] == 'this_month') {
            $inputs['valid_since'] = Carbon::today()->firstOfMonth()->toDateString();
        }
        else {
            $inputs['valid_since'] =  Carbon::today()->firstOfMonth()->addMonth(1)->toDateString();
        }

        $employee_data = EmployeeService::createEmployee($inputs);

        $employee_salary = EmployeeService::setSalary($employee_data, $inputs);
        return response()->json([
            'status' => 'success',
            'message' =>'<b>ID Karyawan:</b> '. $employee_data->employee_id.'<br/><b>Username:</b> '.$new_user['email'].'<br/><b>Password</b>: '.$inputs_user['password'],
            // 'need_reload' => true
        ]);

    }

    function addEmployee()
    {
        $role_user = UserService::getRoleByUser();

        if(strtolower($role_user->slug) == 'superadmin') {
            $branches = Branch::all();
            return view('employee.add-employee',[
                'branches' => $branches,
                'role_slug' => strtolower($role_user->slug)
            ]);
        }

        if(strtolower($role_user->slug) == 'manager') {
            return view('employee.add-employee',[
                'role_slug' => strtolower($role_user->slug)
            ]);
        }
        return "oke";
        abort(404);
    }

    function getEmployees($page)
    {
        // dd($page);
        $take = 20;
        $skip = ($page - 1) * 20;


        $role_user = UserService::getRoleByUser();
        if(strtolower($role_user->slug)=='superadmin') {
            $employees = Employee::get();
            $total = $employees->count();
            $employees = Employee::skip($skip)->take($take)
                            ->with(['branch'])->orderBy('full_name')->get();
            // dd($employees);
            if($employees->count()) {
                return view('employee.employees',[
                    'employees' => $employees,
                    'role_slug' => strtolower($role_user->slug),
                    'message' => HelperService::dataCountingMessage($total, $skip+1, $skip+$employees->count(), $page),
                    'total_page' => ceil($total/$take)
                ]);
            }
            abort(404);
        }
        else if(strtolower($role_user->slug)=='manager') {
            $employee_data = EmployeeService::getEmployeeByUser();
            $employees = Employee::where('branch_id', $employee_data->branch_id)->get();
            $total = $employees->count();
            $employees = Employee::where('branch_id', $employee_data->branch_id)->skip($skip)->take($take)
                            ->with(['branch'])->orderBy('full_name')->get();
            // dd($employees);
            if($employees->count()) {
                return view('employee.employees',[
                    'employees' => $employees,
                    'role_slug' => strtolower($role_user->slug),
                    'message' => HelperService::dataCountingMessage($total, $skip+1, $skip+$employees->count(), $page),
                    'total_page' => ceil($total/$take)
                ]);
            }
            abort(404);
        }

        abort(404);
    }

    function myUser()
    {
        return view('employee.employee-user',[

        ]);
    }

    function getEmployeeAndUser($employee_id, $user_id)
    {
        $employee_data = Employee::where('employee_id',$employee_id)
                                    ->where('user_id', $user_id)
                                    ->first();

        $employee_data_login = EmployeeService::getEmployeeByUser();
        $role_user_login = UserService::getRoleByUser();

        if($employee_data==null) {
            abort(404);
        }
        else if(strtolower($role_user_login->slug)== 'manager' && $employee_data_login->branch_id != $employee_data->branch_id) {
            abort(404);
        }

        $user = User::find($user_id);
        $role_user = UserService::getRoleByUser($user);
        if($role_user==null) {
            $role_user  = Sentinel::getRoleRepository()->findBySlug('staff');
            $role_user->users()->attach($user);
        }

        $employee_salaries = EmployeeSalary::where('employee_id', $employee_id)->orderBy('valid_since', 'desc')->get();
        // dd($user);
        return view('employee.employee-user',[
            'employee_data' => $employee_data,
            'role_user' => $role_user,
            'role_user_login' => $role_user_login,
            'user' => $user,
            'employee_salaries' => $employee_salaries
        ]);
        return "oke";
    }

    function changeRole(Request $request, $employee_id, $user_id)
    {
        $employee_data = Employee::where('employee_id',$employee_id)
                                    ->where('user_id', $user_id)
                                    ->first();

        if($employee_data==null) {
            abort(404);
        }

        $user = User::find($user_id);
        $old_role_user = UserService::getRoleByUser($user);
        $old_role_user->users()->detach($user);

        $inputs = $request->all();
        try {
            $inputs['role_id'] = Crypt::decryptString($inputs['role']);
        } catch (DecryptException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error. Halaman akan reload dan harap coba lagi!',
                'need_reload' => true
            ]);
        }

        $new_role_user = Sentinel::findRoleById($inputs['role_id']);
        $new_role_user->users()->attach($user);
        return response()->json([
            'status' => 'success',
            'message' =>'Role berhasil diganti!',
            'no_reset_form' => true
            // 'need_reload' => true
        ]);
    }

    function setNewSalary(Request $request, $employee_id, $user_id)
    {
        $employee_data = Employee::where('employee_id',$employee_id)
                                    ->where('user_id', $user_id)
                                    ->first();

        if($employee_data==null) {
            abort(404);
        }

        $inputs = $request->all();
        $inputs['employee_salary'] = HelperService::unmaskMoney($inputs['new_salary']);

        if($inputs['salary_since'] == 'this_month') {
            $inputs['valid_since'] = Carbon::today()->firstOfMonth()->toDateString();
        }
        else {
            $inputs['valid_since'] =  Carbon::today()->firstOfMonth()->addMonth(1)->toDateString();
        }

        EmployeeService::setSalary($employee_data, $inputs);

        return response()->json([
            'status' => 'success',
            'need_reload' => true,
            'message' => 'Gaji berhasil diset!',
        ]);
    }

    function deleteSalary($employee_salary_id)
    {
        $employee_salary_id = Crypt::decryptString($employee_salary_id);
        $employee_salary = EmployeeSalary::find($employee_salary_id);

        if($employee_salary) {
            $return = route('get.employee.user', [
                'employee_id'=>$employee_salary->employee_id,
                'user_id'=>$employee_salary->user_id,
            ]);
            $employee_salary->deleted_by = Sentinel::getUser()->id;
            $employee_salary->save();
            $employee_salary->delete();
            return redirect($return);
        }

        abort(404);
    }

    function giveNewPassword(Request $request, $employee_id='0', $user_id=0)
    {
        if($employee_id=='0') {
            $user_id=Sentinel::getUser()->id;
        }
        else {
            $employee_data = Employee::where('employee_id',$employee_id)
                                        ->where('user_id', $user_id)
                                        ->first();

            if($employee_data==null) {
                abort(404);
            }
        }


        $user = User::find($user_id);
        $inputs = $request->all();

        $credentials = [
            'password' => $inputs['password'],
        ];

        $user = Sentinel::update($user, $credentials);
        $message = '<b>Email/Username:</b> '.$user->email;
        $message .= '<br/><b>Password Baru:</b> '.$inputs['password'];
        return response()->json([
            'status' => 'success',
            'message' =>'Password berhasil diganti!<br/>'.$message
            // 'need_reload' => true
        ]);
    }

    function mySalary()
    {
        $employee_data = EmployeeService::getEmployeeByUser();

        if($employee_data) {
            $employee_salaries = EmployeeSalary::where('employee_id', $employee_data->employee_id)
                                        ->where('valid_since','<',Carbon::today()->toDateString())
                                        ->orderBy('valid_since', 'desc')
                                        ->get();
            $salary_now = EmployeeSalary::where('employee_id', $employee_data->employee_id)
                                        ->where('valid_since','<=',Carbon::today()->toDateString())
                                        ->orderBy('valid_since', 'desc')
                                        ->first();
            $transactions = TransactionDetail::with(['header'])->where('item_pic', $employee_data->employee_id)
                                                ->whereDate('created_at','>=', Carbon::now()->firstOfMonth()->toDateString())
                                                ->whereDate('created_at', '<=', Carbon::now()->endOfMonth()->toDateString())
                                                ->get();


            $selects = array(
                'sum(incentive) AS sum_incentive',);
            $sum_incentive =  EmployeeIncentive::where('employee_id', $employee_data->employee_id)
                                                ->whereDate('created_at','>=', Carbon::now()->firstOfMonth()->toDateString())
                                                ->whereDate('created_at', '<=', Carbon::now()->endOfMonth()->toDateString())
                                                ->selectRaw(implode(',', $selects))->get();
            // dd($sum_incentive[0]['sum_incentive']);
            // dd($employee_data->employee_id);
            return view('employee.my-salary',[
                'employee_salaries' => $employee_salaries,
                'salary_now' => $salary_now,
                'transactions' =>$transactions,
                'total_incentive' => $sum_incentive[0]['sum_incentive']
            ]);
        }
        abort(404);
    }

    function unsetIncetives()
    {
        $unset_incentives = TransactionDetail::with(['header','itemInfo','employeeIncentives'])->whereNull('item_pic')
                                ->where('pic_incentive','>',0)
                                ->get();
        // dd($unset_incentives);

        // foreach ($unset_incentives as $key => $value) {
        //     dd($value->employeeIncentives->sum('incentive'));
        // }
        return view('employee.employee-incentives-unset',[
            'unset_incentives' => $unset_incentives
        ]);
    }

    function doSetIncetives(Request $request)
    {
        $inputs = $request->all();
        $detail_id = intval(Crypt::decryptString($inputs['detail']));
        $detail = TransactionDetail::with(['header','itemInfo','employeeIncentives'])->whereNull('item_pic')
                                ->where('pic_incentive','>',0)
                                ->where('id', $detail_id)
                                ->first();
        if($detail) {
            $pb_incentive = [];
            $pb_incentive['item_id'] = $detail->item_id;
            $pb_incentive['qty_item'] = $detail->item_qty;
            // $pb_discount['modal_total'] = $pb_discount['turnover'] = 0 - $potongan_total;
            $already = $detail->employeeIncentives->sum('incentive');
            if($already < $detail->pic_incentive) {
                $employee = Employee::where('employee_id', trim($inputs['employee_id']))->first();
                if($employee) {
                    $incentive_input = [
                        'detail_id' => $detail_id,
                        'employee_id' => $employee->employee_id,
                        'incentive'=> HelperService::unmaskMoney(trim($inputs['incentive'])),
                        'set_by' => Sentinel::getUser()->id,
                    ];
                    // dd($already+$incentive_input['incentive']);
                    DB::beginTransaction();
                    $incentive = EmployeeIncentive::create($incentive_input);
                    $pb_incentive['header_id'] = $detail->header_id;
                    $pb_incentive['detail_id'] = $detail->id;
                    $pb_incentive['modal_total'] = $incentive_input['incentive'];
                    $pb_incentive['branch_id'] = $employee->branch_id;
                    $pb_incentive['description'] = 'Insetif Karyawan: '.HelperService::maskMoney(intval($incentive_input['incentive']));
                    $pb_incentive['turnover'] = 0;
                    $pb_incentive['profit'] = $pb_incentive['turnover']-$pb_incentive['modal_total'];
                    PembukuanBranch::create($pb_incentive);
                    if($already+$incentive_input['incentive'] >= $detail->pic_incentive) {
                         $detail->item_pic = 'ok';
                         $detail->save();
                    }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Insetif berhasil dibagikan!',
                        'need_reload' => true
                    ]);
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Error1. Insetif sudah pernah dibagikan semua!',
                'need_reload' => true
            ]);
        }

        return "404";

    }
}
