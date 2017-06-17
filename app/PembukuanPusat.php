<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PembukuanPusat extends Model
{
    protected $guarded = ['id'];

    protected $table = 'pembukuan_pusat';

    function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_buyer');
    }

    function itemInfo()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id');
    }
}
