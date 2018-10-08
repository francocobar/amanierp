<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;
use DateTime;

class UpdateEmployeeId2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:generatenewid';

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
        $all_employee = Employee::orderBy('work_since')->get();
        $counter = 1;
        foreach ($all_employee as $key => $employee) {
            $this->info('old id: '.$employee->old_employee_id);
            if($employee->work_since) {
                $this->info('work since: '.DateTime::createFromFormat('Y-m-d H:i:s', $employee->work_since)->format('d/m/Y'));
                $new_id = DateTime::createFromFormat('Y-m-d H:i:s', $employee->work_since)->format('ym');
                $new_id = $new_id. sprintf('%04d', $counter);
                $this->info('new id: '. $new_id);
                $this->info('****************************');
                $employee->employee_id = $new_id;
                $employee->save();
                $counter++;
            }
        }
    }
}
