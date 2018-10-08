<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Employee;

class ForTesting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing:code';

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
        $latest_employee_id = Employee::orderBy('employee_id', 'desc')->where('employee_id','not like', 'e%')->first();
        if($latest_employee_id) {
            $latest_suffix = intval(substr($latest_employee_id->employee_id, 4, 4));
            $new_suffix = sprintf('%04d', $latest_suffix+1);
            $this->info($new_suffix);
        }
    }
}
