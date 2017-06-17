<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PembukuanBranch extends Model
{
    protected $guarded = ['id'];

    function itemInfo()
    {
        return $this->hasOne('App\Item', 'item_id', 'item_id');
    }

    function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    function getDetailModal()
    {
        $descs = explode(",",$this->description);
        $return = "";
        foreach ($descs as $key => $desc) {
            if(!empty(trim($desc))) {
                $return .= trim($desc)."<br/>";
            }
        }
        return $return;
    }

    function getDetailTurnoverDesc()
    {
        $descs = explode(",",$this->turnover_description);
        $return = "";
        foreach ($descs as $key => $desc) {
            if(!empty(trim($desc))) {
                $return .= trim($desc)."<br/>";
            }
        }
        return $return;
    }
}
