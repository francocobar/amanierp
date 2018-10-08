<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\CreateFirstUsers',
        'App\Console\Commands\SetStaffRolesDefatult',
        'App\Console\Commands\UpdateMemberData',
        'App\Console\Commands\UpdateIncentives',
        'App\Console\Commands\CheckMissedIncentive',
        'App\Console\Commands\UpdateQtyDone',
        'App\Console\Commands\GivePermissionRemoveTrans',
        'App\Console\Commands\UpdateEmployeeId',
        'App\Console\Commands\UpdateEmployeeId2',
        'App\Console\Commands\ForTesting',
        'App\Console\Commands\CheckNewId',
        'App\Console\Commands\UpdateTableSalary',
        'App\Console\Commands\UpdateTableIncentive'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
