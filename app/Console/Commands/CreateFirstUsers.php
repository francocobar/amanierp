<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sentinel;
use App\User;

class CreateFirstUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:createfirstusers';

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
        $role = Sentinel::getRoleRepository()->findBySlug('superadmin');
        if($role) {
            $this->info('role superadmin already exists!');
        }
        else {
            $role = Sentinel::getRoleRepository()->createModel()->create([
                'name' => 'Superadmin',
                'slug' => 'superadmin',
            ]);
            $this->info('role superadmin is created!');

            $role2 = Sentinel::getRoleRepository()->createModel()->create([
                'name' => 'Admin',
                'slug' => 'manager',
            ]);
            $this->info('role manager is created!');

            $role3 = Sentinel::getRoleRepository()->createModel()->create([
                'name' => 'Staff',
                'slug' => 'staff',
            ]);
            $this->info('role staff is created!');
        }


        $credentials['first_name'] = 'Franco';
        $credentials['last_name'] = 'Escobar';
        $credentials['email'] = 'escobar@franco.web.id';
        $credentials['password'] = '$01101995%';
        if(User::where('email',$credentials['email'])->first()==null)
        {
            $user = Sentinel::registerAndActivate($credentials);
            $role->users()->attach($user);
            $this->info($credentials['first_name'].' is created!');
        }

        $credentials['first_name'] = 'Heri';
        $credentials['last_name'] = 'Harjoni';
        $credentials['email'] = 'heriharjoni';
        $credentials['password'] = '$12345%';
        if(User::where('email',$credentials['email'])->first()==null)
        {
            $user = Sentinel::registerAndActivate($credentials);
            $role->users()->attach($user);
            $this->info($credentials['first_name'].' is created!');
        }

        $credentials['first_name'] = 'Yohana';
        $credentials['last_name'] = 'Andriani';
        $credentials['email'] = 'yohana';
        $credentials['password'] = '$09876%';
        if(User::where('email',$credentials['email'])->first()==null)
        {
            $user = Sentinel::registerAndActivate($credentials);
            $role->users()->attach($user);
            $this->info($credentials['first_name'].' is created!');
        }

        return "oke";
    }
}
