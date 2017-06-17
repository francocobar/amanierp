<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    function itemInfo()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id');
    }
}
