<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferStock extends Model
{
    protected $fillable = ['item_id', 'branch_id', 'stock', 'sender', 'approval', 'approval_status',
                            'approval_date', 'sender_note', 'approval_note'];

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
