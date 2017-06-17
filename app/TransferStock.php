<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferStock extends Model
{
    protected $guarded = ['id'];

    function item()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id');
    }

    function user()
    {
        return $this->hasOne('App\User', 'id', 'sender');
    }

    function approver()
    {
        return $this->hasOne('App\User', 'id', 'approval');
    }
}
