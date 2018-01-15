<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $primaryKey = 'member_id'; // or null
    public $incrementing = false;
    protected $fillable = ['member_id', 'full_name', 'email', 'dob', 'member_since', 'address', 'region_id',
                            'phone', 'branch_id', 'created_by'];

    public function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }
}
