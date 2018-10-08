<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;
use App\EmployeeSalary;

class UpdateTableSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:updatetablesalary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $all_employee = Employee::all();

        foreach ($all_employee as $key => $employee) {
            if($employee->old_employee_id != $employee->employee_id)
            {
                $salaries = EmployeeSalary::withTrashed()->where('employee_id', $employee->old_employee_id)->get();
                foreach($salaries as $salary)
                {
                    $salary->employee_id = $employee->employee_id;
                    $salary->save();
                    $this->info($employee->employee_id);
                }
                $this->info('****************************');
            }
        }
    }
}
