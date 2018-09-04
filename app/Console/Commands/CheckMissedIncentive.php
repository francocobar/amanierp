<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Item;
use HelperService;

class CheckMissedIncentive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:missedincentive';

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
        $all_jasa = Item::whereIn('item_type', [2,4])->get();
        foreach ($all_jasa as $key => $item) {
            $string = '*#'.$item->item_id.' '.$item->item_name.'* insentif: ';
            if($item->jasaIncentive) {
                $string.= ' '.HelperService::maskMoney($item->jasaIncentive->incentive);
            }
            else {
                $string.='0';
            }
            echo "\n";
            $this->info($string);
        }
    }
}
