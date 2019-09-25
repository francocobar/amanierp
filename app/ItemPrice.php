<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    protected $guarded = [];

    protected $visible = ['nm_price','m_price'];
}
