<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Branch;
use Sentinel;
class BranchServiceProvider extends ServiceProvider
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

    static function addBranch($inputs)
    {
        $inputs['created_by'] = Sentinel::getUser()->id;
        return Branch::create($inputs);
    }

    static function pembukuanBranchByPaket($inputs)
    {
        $paket_configurations = isset($param['paket_configurations']) ? $param['paket_configurations'] : JasaConfiguration::where('item_id_jasa', $inputs['item_id_paket'])->get();

        $pb = [];
        $pb_modal_per_item = [];
        foreach ($paket_configurations  as $jasa) {
            $jasa_configurations = JasaConfiguration::where('item_id_jasa', $inputs['item_id_jasa'])->get();
            $qty_jasa = $inputs['qty'] * $jasa->qty_jasa;
            foreach ($jasa_configurations  as $config) {
                $branch_stock = BranchStock::with(['itemInfo'])->where('branch_id', $inputs['branch_id'])
                                        ->where('item_id', $config->item_id_produk)
                                        ->first();
                $berkurang = $qty_jasa * HelperService::round_up($config->pembilang/$config->penyebut,1);
                $modal_pb_detail['modal'] = $bekurang * $branch_stock->modal_per_pcs;
                $modal_per_paket += $modal_pb_detail['modal'];

            }
        }

        return "";

    }
}
