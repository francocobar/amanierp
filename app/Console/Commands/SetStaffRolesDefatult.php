<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Sentinel;
use UserService;

class SetStaffRolesDefatult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:setdefaultroles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'yg belum ada role, jadikan role staff';

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
        $role = Sentinel::getRoleRepository()->findBySlug('staff');
        $users = User::all();
        foreach ($users as $key => $user) {
            if(UserService::getRoleByUser($user)==null) {
                $role->users()->attach($user);
                $this->info($key);
            }
        }
    }
}
