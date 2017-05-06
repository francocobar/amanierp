<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BranchService;
use DB;
use App\Branch;
use Sentinel;
use UserService;
use HelperService;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('superadmin');
    }

    function addBranch()
    {
        return view('branch.add-branch');
    }

    function addBranchDo(Request $request)
    {
        if($request->ajax()) {
            $inputs = $request->all();
            unset($inputs['_token']);
            $inputs['branch_name'] = ucwords(trim(strtolower($inputs['branch_name'])));
            $inputs['phone'] = trim($inputs['phone']);
            $inputs['address'] = trim($inputs['address']);
            $inputs['prefix'] = strtoupper(trim($inputs['prefix']));
            $already = Branch::where('branch_name','like', $inputs['branch_name'])
                        ->orWhere('prefix','like',$inputs['prefix'])->get();

            $message = '';

            if($already->where('branch_name',$inputs['branch_name'])->count() > 0) {
                $message .= 'Nama Cabang';
            }

            if($already->where('prefix',$inputs['prefix'])->count() > 0) {
                $message .= $message == '' ? 'Prefix' : ', Prefix';
            }

            if($message != '') {
                return response()->json([
                    'status' => 'error',
                    'message' => $message.' sudah pernah didaftarkan.'
                ]);
            }

            BranchService::addBranch($inputs);
            return response()->json([
                'status' => 'success',
                'message' => 'Cabang berhasil ditambahkan!'
            ]);
        }

        abort('404');
    }

    function getBranches($page)
    {
        $take = 20;
        $skip = ($page - 1) * $take;
        $total = Branch::get()->count();
        $role_user = UserService::getRoleByUser();
        $branches = Branch::skip($skip)->take($take)
                        ->orderBy('branch_name')->get();

        if($branches->count())
            return view('branch.branches', [
                'branches' => $branches,
                'message' => HelperService::dataCountingMessage($total, $skip+1, $skip+$branches->count(), $page),
                'total_page' => ceil($total/$take),
            ]);

        abort(404);
    }
}
