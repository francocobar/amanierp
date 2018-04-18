<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sentinel;

class GivePermissionRemoveTrans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:changeStatusTrans {userId} {action?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'memberikan akses ke user untuk remove transaksi';

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
        $userId = $userId = $this->argument('userId');
        $actionName = !empty($this->argument('action')) ? $this->argument('action') : 'add';
        $user = Sentinel::findById($userId);
        if($actionName == 'add')
        {
            $user->addPermission('changeStatus.trans');
            $user->save();
            $this->info('permission has been added!');
        }
        else if($actionName == 'remove')
        {
            $user->removePermission('changeStatus.trans');
            $user->save();
            $this->info('permission has been removed!');
        }


    }
}
