<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Branch;
use Constant;
use Carbon\Carbon;
use HelperService;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    static function randomStr($length=10, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    static function round_up($value, $places)
    {
        $mult = pow(10, abs($places));
        return $places < 0 ?
        ceil($value / $mult) * $mult :
        ceil($value * $mult) / $mult;
    }

    static function getPeriod($period_id)
    {
        if(intval($period_id)==Constant::daily_period) {
            return 'Harian';
        }
        return 'Bulanan';
    }

    static function getPrefixInvoice($branch_id=0)
    {
        if($branch_id==0) {
            $branch_id = 5;
        }
        $prefix_branch = strtoupper(Branch::find($branch_id)->prefix);
        $date = '/'.str_replace('-','',Carbon::today()->toDateString()).'/';
        return 'INV/'.$prefix_branch.$date;
    }

    static function getPrefixEmployeeId($branch_id)
    {
        return 'E'.Branch::find($branch_id)->prefix;
    }

    static function getPrefixMemberId($branch_id)
    {
        return Branch::find($branch_id)->prefix;
    }

    static function dataCountingMessage($total, $from, $until, $page=0)
    {
        $message = '';
        if($page>0)
            $message = 'Halaman '.$page.': ';
        $message .= 'Menampilkan '.($from).' - '.($until). ' dari '.$total;
        return $message;
    }

    static function itemTypeById($id)
    {
        $types = [
            1 => 'produk',
            2 => 'jasa',
            3 => 'sewa',
            4 => 'paket'
        ];
        if(isset($types[$id])) {
            return $types[$id];
        }

        return 'Tipe tidak terdefinisi!';
    }

    static function arrayMonth()
    {
       return array(
           1 => 'Januari',
           2 => 'Februari',
           3 => 'Maret',
           4 => 'April',
           5 => 'Mei',
           6 => 'Juni',
           7 => 'Juli',
           8 => 'Agustus',
           9 => 'September',
           10 => 'Oktober',
           11 => 'Nopember',
           12 => 'Desember'
       );
    }
    static function monthName($int_month)
    {
        $array_month = HelperService::arrayMonth();
        return $array_month[intval($int_month)];
        // if(intval($int_month)==1) {
        //     return "Januari";
        // }
        // else if(intval($int_month)==2) {
        //     return "Februari";
        // }
        // else if(intval($int_month)==3) {
        //     return "Maret";
        // }
        // else if(intval($int_month)==4) {
        //     return "April";
        // }
        // else if(intval($int_month)==5) {
        //     return "Mei";
        // }
        // else if(intval($int_month)==6) {
        //     return "Juni";
        // }
        // else if(intval($int_month)==7) {
        //     return "Juli";
        // }
        // else if(intval($int_month)==8) {
        //     return "Agustus";
        // }
        // else if(intval($int_month)==9) {
        //     return "September";
        // }
        // else if(intval($int_month)==10) {
        //     return "Oktober";
        // }
        // else if(intval($int_month)==11) {
        //     return "Nopember";
        // }
        // else if(intval($int_month)==12) {
        //     return "Desember";
        // }
    }
    static function inaDate($db_date, $type=1)
    {
        if($type==1) {//date only
            $exploded = explode('-', $db_date);

            return $exploded[2].' '.HelperService::monthName($exploded[1]).' '.$exploded[0];
        }


        //datetime
        return HelperService::inaDate($db_date->toDateString()).' '.$db_date->toTimeString();

    }
    static function createDateFromString($dd_mm_yyyy)
    {
        $dob = explode('-', $dd_mm_yyyy);
        // dd($dob);
        // return $dob;
        return $dob[2].'-'.$dob[1].'-'.$dob[0];
    }

    static function generatePaging($page, $total_page)
    {
        $first = $last = '';
        $first_href = $last_href = $prev_href = $next_href = '#';

        if($page==1) {
            $first='disabled';
        }
        else if($page>1){
            $first_href = 1;
            $prev_href = $page-1;
        }
        if($page==$total_page) {
            $last='disabled';
        }
        else if($page<$total_page) {
            $next_href = $page+1;
            $last_href =$total_page;
        }

        $paging = '';

        $start = 1;
        if($page > 5 ) {
            $start = $page - 2;
        }
        $max = $total_page;
        if($total_page - 20 > $start + 20) {
            $max = $start + 20;
        }
        for ($i=$start; $i <= $max ; $i++) {
            $paging .= "<li class='".($i==$page?'active':'')."'>
                <a href='".$i."?name=".request()->name."'>".$i."</a>
            </li>";
        }

        return "<div class='dataTables_paginate paging_bootstrap_full_number text-center'>
            <ul class='pagination' style='visibility: visible;'>
                <li class='prev ".$first."'>
                    <a href='".$first_href."' title='Halaman Pertama'>
                        <i class='fa fa-angle-double-left'></i>
                    </a>
                </li>
                <li class='prev ".$first."'>
                    <a href='".$prev_href."' title='Sebelumnya'>
                        <i class='fa fa-angle-left'></i>
                    </a>
                </li>".$paging
                ."<li class='next ".$last."'>
                    <a href='".$next_href."' title='Berikutnya'><i class='fa fa-angle-right'></i></a>
                </li>
                <li class='next ".$last."'>
                    <a href='".$last_href."' title='Terakhir'><i class='fa fa-angle-double-right'></i></a>
                </li>
            </ul>
        </div>";
    }

    static function maskMoney($money, $float=false)
    {
        if($float) {
            return number_format($money, 2, ',', '.');
        }
        return number_format(intval($money),0,",",".");
    }

    static function unmaskMoney($masked_money)
    {
        return str_replace('.', '', $masked_money);
    }
}
