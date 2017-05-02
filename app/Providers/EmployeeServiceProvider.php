<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Employee;
use App\EmployeeSalary;
use Sentinel;
class EmployeeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public static function createEmployee($inputs)
    {
        $inputs['created_by'] = Sentinel::getUser()->id;
        return Employee::create($inputs);
    }

    public static function setSalary($employee, $inputs)
    {
        $inputs['created_by'] = Sentinel::getUser()->id;
        $inputs['employee_id'] = $employee->employee_id;
        $inputs['user_id'] = $employee->user_id;
        return EmployeeSalary::create($inputs);
    }


    public static function getEmployeeByUser($user = null)
    {
        if($user==null) {
            $user = Sentinel::getUser();
        }
        $employee_data = Employee::where('user_id', $user->id)->first();

        return $employee_data;
    }
}
