<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Member;

class UpdateMemberData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:memberid';

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
        $all = Member::get();

        foreach ($all as $key => $member) {
            $this->info($member->member_id);
            $member->member_id = strtoupper($member->member_id);
            $member->save();
        }
    }
}
