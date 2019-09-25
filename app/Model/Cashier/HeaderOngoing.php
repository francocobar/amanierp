<?php

namespace App\Model\Cashier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeaderOngoing extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
