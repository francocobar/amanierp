<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JasaIncentive extends Model
{
    protected $fillable = ['item_id_jasa', 'incentive', 'valid_since', 'created_by'];
}
