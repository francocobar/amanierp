<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TransactionDetail;
use \Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\EmployeeIncentive;

class UpdateIncentives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:incentives';

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
        $details = TransactionDetail::whereDate('created_at', Carbon::today())->get();
        $real_incentives = [];
        DB::beginTransaction();
        foreach ($details as $key => $detail) {
            if($detail->itemInfo && ($detail->itemInfo->item_type==2 || $detail->itemInfo->item_type==4)) {
                // $this->info($detail->id.' #'.$detail->header_id);
                $already = 0;
                if($detail->employeeIncentives) {
                    foreach ($detail->employeeIncentives as $key => $emp_incentive) {
                        $real_incentive_this = 0;
                        if(isset($real_incentives[$detail->item_id])) {
                            $real_incentive_this = $real_incentives[$detail->item_id];
                        }
                        else {
                            $real_incentive_this = $real_incentives[$detail->item_id] = $detail->itemInfo->jasaIncentive ? $detail->itemInfo->jasaIncentive->incentive : 0;
                        }
                        if($real_incentive_this != $emp_incentive->incentive) {
                             $emp_incentive->incentive = $real_incentive_this;
                             $emp_incentive->save();
                             $this->info('ditemukan item insentif beda '.$detail->itemInfo->item_name.' pada header id '.$detail->header_id.' invoice'.$detail->header->invoice_id.' yg benar'.$real_incentive_this);
                        }
                        $already++;
                    }
                }
                $kurang = $detail->item_qty_done-$already;
                if($kurang>0){
                    for($i=0;$i<$kurang;$i++) {
                        $this->info('ado yg blum');
                        $new_emp_incentive = new EmployeeIncentive();
                        $new_emp_incentive->detail_id = $detail->id;
                        $new_emp_incentive->employee_id = '';
                        $new_emp_incentive->incentive = $detail->itemInfo->jasaIncentive ? $detail->itemInfo->jasaIncentive->incentive : 0;
                        $new_emp_incentive->branch_id = $detail->header->branch_id;
                        $new_emp_incentive->save();
                        $this->info('ditemukan item insentif blum '.$detail->itemInfo->item_name.' pada header id '.$detail->header_id.' invoice'.$detail->header->invoice_id);
                        if($detail->itemInfo->jasaIncentive) {

                        }
                        else {
                            $this->info('insentif blm diset untuk '.$detail->itemInfo->item_name.' item id '.$detail->itemInfo->item_id);
                        }
                        if($new_emp_incentive->incentive) {
                            $this->info('insentif 0 untuk '.$new_emp_incentive->id);
                        }
                    }
                }
            }
        }
        DB::commit();
    }
}
