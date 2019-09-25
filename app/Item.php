<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Item extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    function itemStock()
    {
        return $this->hasOne('App\Stock', 'item_id', 'item_id');
    }
    function jasaIncentive()
    {
        return $this->hasOne('App\JasaIncentive', 'item_id_jasa', 'item_id')->orderBy('created_at','desc');
    }

    function branchStock()
    {
        return $this->belongsTo('App\BranchStock', 'item_id', 'item_id');
    }

    function itemPrice()
    {
        return $this->hasMany('App\ItemPrice', 'item_id', 'id');
    }

    function isSeries()
    {
        return $this->item_type == 5;
    }
}
