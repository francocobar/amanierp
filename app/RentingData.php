<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentingData extends Model
{
    protected $guarded = ['id'];

    public function itemInfo()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id');
    }

    public function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'renting_branch');
    }

}
