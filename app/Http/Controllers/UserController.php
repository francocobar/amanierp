<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Sentinel;
use UserService;
use Carbon\Carbon;
use HelperService;
use EmployeeService;

use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2', ['only' => 'dashboard']);
    }

    function printTest2()
    {
        $inputs['zzz'] = '';
        dd(intval($inputs['zzz']));
        $connector = null;
        $connector = new WindowsPrintConnector('EPSONPOS');

        $printer = new Printer($connector);
        $printer -> setEmphasis(true);
        $printer -> setUnderline(1);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);

        // $img = EscposImage::load("tux.png");
        // $printer -> graphics($img);
        $printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
        $printer -> text('RUMAH CANTIQUE AMANIE'."\n".'SALON & SPA MUSLIMAH'."\n");$printer -> selectPrintMode(169);
        $printer -> text("\n*** TERIMA KASIH ***\n");
        $printer -> cut();
        $printer -> pulse();
        $printer -> close();
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
            $user_login = [];
            if(UserService::isSuperadmin()) {
                $user_login['branch'] = 'Semua Cabang';
            }
            $employee_data = EmployeeService::getEmployeeByUser();
            if($employee_data) {
                $user_login['full_name'] = $employee_data->full_name;
                if(!isset($user_login['branch'])) {
                    $user_login['branch'] = $employee_data->branch->branch_name;
                }
            }
            else {
                $user = Sentinel::getUser();
                $user_login['full_name'] = $user->first_name.($user->first_name==$user->last_name ? '': ' '.$user->last_name);
            }
            session()->put('user_login',$user_login);
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
