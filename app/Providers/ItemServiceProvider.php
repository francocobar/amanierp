<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Item;
use App\JasaIncentive;
use Sentinel;
use Carbon\Carbon;
use Constant;
use App\Employee;
use App\TransferStock;
use App\JasaConfiguration;
use App\BranchStock;
use HelperService;
use UserService;

class ItemServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    static function addItem($inputs)
    {
        $inputs['created_by'] = Sentinel::getUser()->id;

        $number_id = 1;
        $last_item = Item::orderBy('created_at', 'desc')->first();
        if($last_item != null) {
            $new_number = str_replace('I', '', $last_item->item_id);
            $number_id = $new_number+1;
        }

        $inputs['item_id'] = 'I'.sprintf("%05d", $number_id);
        $incentive = -1;
        if($inputs['item_type'] == Constant::type_id_jasa) {
            $incentive = str_replace('.', '', $inputs['incentive']);
        }
        unset($inputs['incentive']);

        $new_item = Item::create($inputs);

        if($incentive != -1) {
            $create = [];
            $create['item_id_jasa'] = $new_item->item_id;
            $create['incentive'] = $incentive;
            $create['created_by'] = $inputs['created_by'];
            $create['valid_since'] = Carbon::now()->toDateString();

            $item_incentive = JasaIncentive::create($create);
        }
        return $new_item;
    }

    static function getLatestIncentive($item_id_jasa, $return='obj')
    {
        $latest = JasaIncentive::where('item_id_jasa', $item_id_jasa)
                    ->orderBy('created_at', 'desc')
                    ->first();
        if($return=='obj')
            return $latest;

        if($latest) return $latest->incentive;

        return 0;
    }

    // static function countPendingStockConfirmation()
    // {
    //     $employee = Employee::where('user_id', Sentinel::getUser()->id)->first();
    //     if($employee==null) return "";
    //
    //     $pending_confirmation = TransferStock::where('branch_id', $employee->branch_id)
    //                             ->where('approval_status', Constant::status_pending)
    //                             ->count();
    //     return $pending_confirmation;
    // }
}
