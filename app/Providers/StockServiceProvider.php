<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use HelperService;
use App\InputStockLog;
use App\Stock;
use App\BranchStock;
use App\Employee;
use Constant;
use App\TransferStock;
use UserService;
use Illuminate\Support\Facades\DB;
use App\Item;
use App\ModalLog;
use App\JasaConfiguration;
use Sentinel;

class StockServiceProvider extends ServiceProvider
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

    static function inputStockPusat($param, $type=1)
    {

        $item = Item::where('item_id',$param['item_id'])->first();
        if($item) {
            $stock = Stock::where('item_id', $param['item_id'])->first();

            if($stock==null) {
                $stock = new Stock();
                $stock->item_id = $param['item_id'];
                $stock->stock = 0;
                $stock->modal_per_pcs = 0;
            }

            $log = $modal_log = [];
            $log['item_id'] = $param['item_id'];
            $log['modal_per_pcs_before'] = $stock->modal_per_pcs;
            $log['stock_before'] = $stock->stock;
            $total_modal_before = $stock->modal_per_pcs * $stock->stock;


            $log['stock_new_input'] = $stock_new_input = intval($param['add_stock']);
            $log['modal_new_input'] = $modal_new_input = intval(HelperService::unmaskMoney($param['modal_per_pcs']));
            $modal_log['modal_value'] = $total_modal_new_input = $stock_new_input * $modal_new_input;

            $log['stock_after'] = $stock->stock = $stock->stock + $stock_new_input;

            $total_modal_after = $total_modal_before + $total_modal_new_input;

            $log['modal_per_pcs_after'] = $stock->modal_per_pcs = $total_modal_after/$stock->stock;
            $log['input_by'] = Sentinel::getUser()->id;
            $stock->save();

            $log_data = InputStockLog::create($log);

            if($type==1) {
                $modal_log['information'] = 'Penambahan stok pusat #'.$item->item_id.' '.$item->item_name.' sebanyak '.$stock_new_input.' @'. $param['modal_per_pcs'];
                $modal_log['information'] .= ' | No. SPL: '.$log_data->id;
            }
            else if($type==2) {
                $modal_log['information'] = 'Pengembalian stok pusat #'.$item->item_id.' '.$item->item_name.' dari '.$param['branch']->branch_name.' sebanyak '.$stock_new_input.' @'. $param['modal_per_pcs'];
                $modal_log['information'] .= ' | No. SPL: '.$log_data->id;
            }
            else if($type==3) {

            }

            $modal_log['modal_type'] = $type;
            ModalLog::create($modal_log);
            return '';
        }
        return 'Invalid Item Id';
    }

    static function updateBranchStockByJasa($inputs, $array_modal = false)
    {
        $jasa_configurations = JasaConfiguration::where('item_id_jasa', $inputs['item_id_jasa'])->get();

        if($array_modal) {
            $return = [];
            $return['modal_total_per_item'] = 0;
            foreach ($jasa_configurations  as $config) {
                $branch_stock = BranchStock::with(['itemInfo'])->where('branch_id', $inputs['branch_id'])
                                        ->where('item_id', $config->item_id_produk)
                                        ->first();
                // dd($branch_stock->itemInfo());
                if($branch_stock) {
                    // $berkurang = $inputs['qty'] * HelperService::round_up($config->pembilang/$config->penyebut,1);
                    $berkurang = $inputs['qty'] * $config->pembilang/$config->penyebut;
                    $return['modal_per_produk'][$config->item_id_produk] = $branch_stock->modal_per_pcs;
                    $return['qty_produk'][$config->item_id_produk] = $berkurang;
                    $return['modal_total'][$config->item_id_produk] = $return['modal_per_produk'][$config->item_id_produk] *
                                                $return['qty_produk'][$config->item_id_produk];
                    $return['modal_total_per_item'] += $return['modal_total'][$config->item_id_produk];
                    $branch_stock->stock = $branch_stock->stock-$berkurang;
                    if($branch_stock->stock>0){
                        $return['sisa'][$config->item_id_produk] = $branch_stock->stock;
                        $branch_stock->save();
                    }
                    else {
                        $return['error_message'] = "stok <b>".$branch_stock->itemInfo->item_name."</b> menjadi minus, silahkan revisi stok <b>".$branch_stock->itemInfo->item_name."</b> terlebih dahulu!";
                        return $return;
                    }
                }
                else {
                    $items = Item::where('item_id',$config->item_id_produk)->first();
                    $return['error_message'] = "stok <b>".$items->item_name."</b> menjadi minus, silahkan revisi stok <b>".$items->item_name."</b> terlebih dahulu!";
                    return $return;
                }
            }
            $return['error_message'] = '';
            return $return;

        }
        foreach ($jasa_configurations  as $config) {
            $branch_stock = BranchStock::with(['itemInfo'])->where('branch_id', $inputs['branch_id'])
                                    ->where('item_id', $config->item_id_produk)
                                    ->first();
            // dd($branch_stock->itemInfo());
            if($branch_stock) {
                $berkurang = $inputs['qty'] * HelperService::round_up($config->pembilang/$config->penyebut,1);
                $branch_stock->stock = $branch_stock->stock-$berkurang;
                if($branch_stock->stock>0)
                    $branch_stock->save();
                else {
                    return "stok <b>".$branch_stock->itemInfo->item_name."</b> menjadi minus, silahkan revisi stok <b>".$branch_stock->itemInfo->item_name."</b> terlebih dahulu!";
                }
            }
            else {
                $items = Item::where('item_id',$config->item_id_produk)->first();
                return "stok <b>".$items->item_name."</b> menjadi minus, silahkan revisi stok <b>".$items->item_name."</b> terlebih dahulu!";
            }
        }

        return "";
    }

    static function getModalPerJasa($inputs)
    {
        $jasa_configurations = JasaConfiguration::where('item_id_jasa', $inputs['item_id_jasa'])->get();

        foreach ($jasa_configurations  as $config) {
            $branch_stock = BranchStock::with(['itemInfo'])->where('branch_id', $inputs['branch_id'])
                                    ->where('item_id', $config->item_id_produk)
                                    ->first();
            // dd($branch_stock->itemInfo());
            if($branch_stock) {
                $berkurang = $inputs['qty'] * HelperService::round_up($config->pembilang/$config->penyebut,1);
                $branch_stock->stock = $branch_stock->stock-$berkurang;
                if($branch_stock->stock>0)
                    $branch_stock->save();
                else {
                    return "stok <b>".$branch_stock->itemInfo->item_name."</b> menjadi minus, silahkan revisi stok <b>".$branch_stock->itemInfo->item_name."</b> terlebih dahulu!";
                }
            }
        }
    }

    static function getApprovedStockConfirmation()
    {
        if(UserService::isSuperadmin()) {
            return TransferStock::where('approval_status', Constant::status_approved)
                                    ->orderBy('approval_date','desc')->get();
        }
        $employee = Employee::where('user_id', Sentinel::getUser()->id)->first();
        if($employee==null) return "";

        return TransferStock::where('branch_id', $employee->branch_id)
                                ->where('approval_status', Constant::status_approved)
                                ->get();
    }

    static function getRejectedStockConfirmation($unseen=0, $update=true)
    {
        if(UserService::isSuperadmin()) {
            if($unseen==1) {
                $unseen = TransferStock::where('approval_status', Constant::status_rejected)
                                    ->where('response_seen', false)
                                    ->orderBy('approval_date','desc')->get();
                if($update)
                    TransferStock::where('approval_status', Constant::status_rejected)
                                    ->where('response_seen', false)
                                    ->orderBy('approval_date','desc')
                                    ->update(['response_seen'=>true]);

                return $unseen;
            }

            return TransferStock::where('approval_status', Constant::status_rejected)
                                ->orderBy('approval_date','desc')->get();
        }
        $employee = Employee::where('user_id', Sentinel::getUser()->id)->first();
        if($employee==null) return "";

        return TransferStock::where('branch_id', $employee->branch_id)
                                ->where('approval_status', Constant::status_rejected)
                                ->get();
    }

    static function getPendingStockConfirmation()
    {
        if(UserService::isSuperadmin()) {
            return TransferStock::where('approval_status', Constant::status_pending)
                                    ->orderBy('approval_date','desc')->get();
        }
        $employee = Employee::where('user_id', Sentinel::getUser()->id)->first();
        if($employee==null) return "";

        return TransferStock::where('branch_id', $employee->branch_id)
                                ->where('approval_status', Constant::status_pending)
                                ->get();
    }

}
