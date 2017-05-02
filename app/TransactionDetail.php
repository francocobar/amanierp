<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $guarded = ['id'];

    public function itemInfo()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id');
    }

    public function header()
    {
        return $this->hasOne('App\TransactionHeader', 'id', 'header_id');
    }
}
