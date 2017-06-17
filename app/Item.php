<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Item extends Model
{
    protected $fillable = ['item_id', 'item_name', 'item_type', 'description', 'branch_price', 'm_price', 'nm_price', 'created_by'];

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
        return $this->hasOne('App\JasaIncentive', 'item_id_jasa', 'item_id');
    }

    function branchStock()
    {
        return $this->belongsTo('App\BranchStock', 'item_id', 'item_id');
    }
}
