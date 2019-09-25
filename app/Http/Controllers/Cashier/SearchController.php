<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Crypt;
use App\Item;
use App\Model\Cashier\HeaderOngoing;
use App\Member;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }

    protected function searchItems(HeaderOngoing $header, $branch_id)
    {
        $price_key = $header->member_id ? 'm_price' : 'nm_price';
        // return $branch_id;
        $returns = Item::where('for_sale', 1)
                        ->where(function($q){
                            $q->where('item_name', 'like', '%'.request()->term.'%')
                                ->orWhere('item_id', 'like', '%'.request()->term.'%');
                        })
                        ->orderBy('item_name')
                        ->with(['itemPrice' => function ($query) use ($branch_id) {
                            $query->where('branch_id', $branch_id);
                        }])
                        ->get(['item_name','item_id','id', 'item_type']);

        $returns_ = array();
        foreach ($returns as $key => $return) {
            if(!$return->isSeries()) {
                $return_['item_code'] = $return->item_id;
                $return_['item_name'] = $return->item_name;
                $return_['price'] = $return->itemPrice[0]->$price_key;
                $return_['flag'] = $header->id;
                $returns_[] = $return_;
            }

        }
        return response()->json($returns_);
    }

    protected function searchSeriesItems(HeaderOngoing $header, $branch_id)
    {
        $price_key = $header->member_id ? 'm_price' : 'nm_price';
        // return $branch_id;
        $returns = Item::where('for_sale', 1)
                        ->where(function($q){
                            $q->where('item_name', 'like', '%'.request()->term.'%')
                                ->orWhere('item_id', 'like', '%'.request()->term.'%');
                        })
                        ->orderBy('item_name')
                        ->with(['itemPrice' => function ($query) use ($branch_id) {
                            $query->where('branch_id', $branch_id);
                        }])
                        ->get(['item_name','item_id','id','item_type']);

        $returns_ = array();
        foreach ($returns as $key => $return) {
            if($return->isSeries()) {
                $return_['item_code'] = $return->item_id;
                $return_['item_name'] = $return->item_name;
                $return_['price'] = $return->itemPrice[0]->$price_key;
                $return_['flag'] = $header->id;
                $returns_[] = $return_;
            }
        }
        return response()->json($returns_);
    }

    protected function searchMembers()
    {
        $members = Member::where('full_name', 'like', '%'.request()->term.'%')
                        ->orWhere('member_id', 'like', '%'.request()->term.'%')
                        ->take(30)->get(['member_id', 'full_name','phone', 'address'])->toArray();

        return response()->json($members);
    }
}
