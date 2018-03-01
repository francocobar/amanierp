<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use UserService;
use App\Branch;
use App\Member;
use HelperService;
use MemberService;
use EmployeeService;

class MemberController extends Controller
{

    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager');
    }

    function addMember()
    {
        if(strtolower($this->role_user->slug) == 'superadmin') {
            $branches = Branch::all();
            return view('member.add-member',[
                'branches' => $branches,
                'role_slug' => strtolower($role_user->slug)
            ]);
        }

        if(strtolower($role_user->slug) == 'manager') {
            return view('member.add-member',[
                'role_slug' => strtolower($role_user->slug)
            ]);
        }

        abort(404);
    }

    function addMemberDo(Request $request)
    {
        // return "oke";
        $inputs = $request->all();

        $inputs['full_name'] = ucwords(trim(strtolower($inputs['full_name'])));
        $inputs['email'] = trim($inputs['email']);
        $inputs['address'] = trim($inputs['address']);
        $inputs['phone'] = trim($inputs['phone']);
        $inputs['place_of_birth'] = trim($inputs['place_of_birth']);
        $inputs['dob'] = HelperService::createDateFromString(trim($inputs['dob']));
        // dd($inputs['dob']);
        $inputs['member_since'] = HelperService::createDateFromString(trim($inputs['member_since']));
        $inputs['stay_at'] = trim($inputs['stay_at']);

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

        unset($inputs['_token']);
        unset($inputs['branch']);

        $add_member = MemberService::addMember($inputs);
        if(isset($add_member['error']))
            return response()->json([
                'status' => 'error',
                'message' => $add_member['error'],
            ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Member baru, <b>Nama:</b> '.$add_member->full_name.', berhasil ditambahkan. <b>Member Id:</b> '.$add_member->member_id,
        ]);
    }
    function getMembersByAjax(Request $request)
    {
        $members = Member::whereIn('member_id', $request->input('member_id'))
                    ->get(['member_id','full_name']);
        return array('data' => $members);
    }
    function getMembers($page)
    {
        // dd($page);
        $take = 20;
        $skip = ($page - 1) * $take;

        $members = null;
        $role_user = UserService::getRoleByUser();
        if(strtolower($role_user->slug)=='superadmin') {
            $members = Member::get();
            $total = $members->count();
            $members = Member::skip($skip)->take($take)
                            ->with(['branch'])->orderBy('full_name')->get();
            // dd($employees);
        }
        else if(strtolower($role_user->slug)=='manager') {
            $employee_data = EmployeeService::getEmployeeByUser();
            $members = Member::where('branch_id', $employee_data->branch_id)->get();
            $total = $members->count();
            $members = Member::where('branch_id', $employee_data->branch_id)->skip($skip)->take($take)
                            ->with(['branch'])->orderBy('full_name')->get();
            // dd($employees);
        }
        if($members!= null && $members->count()) {
            return view('member.members',[
                'members' => $members,
                'role_slug' => strtolower($role_user->slug),
                'message' => HelperService::dataCountingMessage($total, $skip+1, $skip+$members->count(), $page),
                'total_page' => ceil($total/$take)
            ]);
        }
        abort(404);
    }

}
