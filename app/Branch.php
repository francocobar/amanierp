<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $fillable = ['branch_name', 'address', 'phone', 'prefix', 'created_by'];
}
