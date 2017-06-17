<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaketConfiguration extends Model
{
    protected $guarded = ['id'];

    function jasa()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id_jasa');
    }
}
