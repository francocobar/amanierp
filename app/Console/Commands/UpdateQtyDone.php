<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TransactionDetail;

class UpdateQtyDone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:qtydone';

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
        $details = TransactionDetail::whereMonth('created_at', 9)->whereYear('created_at', 2018)->get();
        foreach ($details as $key => $detail) {
            if($detail->created_at->format('d')<4) {
                if($detail->header->status==2 && $detail->header->branch_id !=1) {
                    $detail->item_qty_done = $detail->item_qty;
                    $detail->save();
                }

            }
        }
    }
}
