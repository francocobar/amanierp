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
    protected $fillable = ['member_id', 'full_name', 'email', 'dob', 'member_since', 'address',
                            'phone', 'branch_id', 'place_of_birth', 'stay_at', 'created_by', 'deleted_by'
                            , 'deleted_reason'];

    public function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    function edit_member_id_url()
    {
        $id_url = str_replace(' ', '-', $this->member_id);
        return route('edit.member', $id_url);
    }

    function dob_form_value()
    {
        $exploded = explode('-', $this->dob);
        return $exploded[2].'-'.$exploded[1].'-'.$exploded[0];
    }

    function membersince_form_value()
    {
        $exploded = explode('-', $this->member_since);
        return $exploded[2].'-'.$exploded[1].'-'.$exploded[0];
    }
}
