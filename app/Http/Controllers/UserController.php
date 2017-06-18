<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Sentinel;
use UserService;
use Carbon\Carbon;
use HelperService;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2', ['only' => 'dashboard']);
    }

    function testing()
    {
        return HelperService::round_up(2.0, 1);
        return Carbon::today()->firstOfMonth()->addMonth(1)->toDateString();
    }
    function createFirstUser()
    {
        $credentials = [
            'email'    => 'francoescobar',
            'password' => 'password',
            'first_name' => 'Franco  ',
            'last_name' => 'Escobar',
        ];
        dd(UserService::createUser($credentials, true));
    }



    function login()
    {
        // dd($request);
        if(Sentinel::getUser()) {
            return redirect()->route('dashboard');
        }
        return view('login');
    }

    function loginDo(Request $request)
    {
        $inputs = $request->all();
        if(empty($inputs['email']) || empty($inputs['password'])) {
            return redirect('login')->withInput()->with('general-error', 'Email/Username dan Password harus diisi!');
        }
        $credentials = [
            'email'    => trim($inputs['email']),
            'password' => $inputs['password'],
        ];

        $login = UserService::login($credentials);
        if($login == '') {
            if(session('redirect')) {
                $redirect = session('redirect');
                session()->forget('redirect');
                return redirect($redirect);
            }
            return redirect()->route('dashboard');
        }
        return redirect('login')->withInput()->with('general-error', $login);

    }

    function logout()
    {
        session()->flush();
        Sentinel::logout();
        return redirect()->route('login');
    }

    function dashboard()
    {
        $role_user_login = UserService::getRoleByUser();
        // dd($role_user_login);
        return view('employee.employee-user',[
            'role_user_login' => $role_user_login
        ]);
    }
}