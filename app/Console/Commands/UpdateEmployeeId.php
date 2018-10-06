<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;

class UpdateEmployeeId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:keepoldid';

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
            $employee->old_employee_id = $employee->employee_id;
            $employee->save();
            // code...
        }
    }
}
