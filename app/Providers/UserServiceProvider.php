<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sentinel;
use App\User;
use UserService;
use EmployeeService;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // date_default_timezone_set('Asia/Jakarta');
    }

    public static function test()
    {
        return "okesss";
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public static function createUser($credentials, $is_first_user = false)
    {
        $credentials['first_name'] = trim($credentials['first_name']);
        $credentials['last_name'] = trim($credentials['last_name']);
        // if(!isset($credentials['email']) || empty($credentials['email'])) {
        //     $credentials['email'] = strtolower(substr($credentials['first_name'].$credentials['last_name'],0,6));
        //     $i=1;
        //
        //     $credentials['email'] .= $i;
        //     while(User::where('email',$credentials['email'])->first()!=null) {
        //         $i++;
        //         $credentials['email'] .= $i;
        //     }
        // }
        // else {
        //     if(User::where('email',$credentials['email'])->first()!=null) {
        //         return "Email/Username sudah terdaftar!";
        //     }
        // }

        $user = Sentinel::registerAndActivate($credentials);

        // if($is_first_user) {
        //     $role = Sentinel::getRoleRepository()->createModel()->create([
        //         'name' => 'Superadmin',
        //         'slug' => 'superadmin',
        //     ]);
        //
        //     $role->users()->attach($user);
        // }
        return $user;
    }

    public static function login($credentials)
    {
        if(Sentinel::authenticate($credentials)) {
            Sentinel::getUser();
            $employee_data = EmployeeService::getEmployeeByUser();
            if($employee_data) session(['branch_id' => $employee_data->branch_id]);
            return "";
        }
        return "Email/Username atau Password salah!";
    }

    public static function isSuperadmin($user = null)
    {
        if($user==null) {
            $user = Sentinel::getUser();
        }

        $role = UserService::getRoleByUser($user);
        // return $role;
        if($role == null || strtolower($role->slug) != 'superadmin')
            return false;

        return true;
    }

    public static function isManager($user = null)
    {
        if($user==null) {
            $user = Sentinel::getUser();
        }

        $role = UserService::getRoleByUser($user);
        // return $role;
        if($role == null || strtolower($role->slug) != 'manager')
            return false;

        return true;
    }

    public static function getRoleByUser($user = null)
    {
        if($user==null) {
            $user = Sentinel::getUser();
        }
        // return $user->id;
        $obj_role_user = User::find($user->id)->roleUser;
        if($obj_role_user != null) {
            $role = Sentinel::findRoleById($obj_role_user->role_id);
            return $role;
        }
        return null;
    }
}
