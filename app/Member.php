<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Member extends Model
{



    use SoftDeletes;
   /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'member_id'; // or null
    public $incrementing = false;
    protected $fillable = ['member_id', 'full_name', 'email', 'dob', 'member_since', 'address', 'region_id',
                            'phone', 'branch_id', 'created_by'];

    public function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }
}
