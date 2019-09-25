<?php

namespace App\Helper;
use App\Item;
use App\ItemPrice;
use Illuminate\Support\Facades\Cache;


class ItemHelper
{

    protected $item_id = 'zz';
    public function __construct()
    {

    }

    static function getItemInfoByItemCode($item_code)
    {
        $key = 'info_itemcode_'.$item_code;
        $info = Cache::get($key);
        if($info == null) {
            $info = Item::where('item_id', trim(request()->item_code))
                            ->first();
            if($info) {
                Cache::put($key, $info, 43200);
            }
        }
        return $info;
    }

    static function getItemPriceByItemIdAndBranch($item_id, $branch_id)
    {
        $key = 'price_itemid_'.$item_id.'_branch_'.$branch_id;
        $price = Cache::get($key);
        if($price == null) {
            $price = ItemPrice::where('item_id', $item_id)
                            ->where('branch_id', $branch_id)
                            ->first();
            if($price) {
                Cache::put($key, $price, 43200);
            }
        }
        return $price;
    }
}
