<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use HelperService;
use App\NextPayment;

class TransactionHeader extends Model
{
    protected $guarded = ['id'];

    function totalTransaction($for_view=false)
    {
        $return = $this->grand_total_item_price+$this->others-$this->total_item_discount-$this->discount_total_fixed_value;

        return $for_view ? HelperService::maskMoney($return) : $return;
    }

    function totalIdrDiscount($for_view=false)
    {
        $return = $this->total_item_discount+$this->discount_total_fixed_value;
        return $for_view ? HelperService::maskMoney($return) : $return;
    }

    function firstPayment($for_view=false)
    {
        $return = $this->total_paid-$this->change;

        return $for_view ? HelperService::maskMoney($return) : $return;
    }

    function paidValue($for_view=false)
    {
        $return = 0;
        if($this->status == 2 || $this->status == 4)
        {
            $first_payment = $this->firstPayment();
            $next_payment = NextPayment::where('header_id', $this->id)
                            ->groupBy('header_id')->sum('paid_value');
            $return = $first_payment+$next_payment;
        }

        return $for_view ? HelperService::maskMoney($return) : $return;
    }

    function debtValue($for_view=false)
    {
        $return = 0;
        if($this->status == 2)
        {
            $last_payment = NextPayment::where('header_id', $this->id)->orderBy('created_at','desc')->first();
            $return = $last_payment ? $last_payment->debt_after : $this->debt;
        }
        return $for_view ? HelperService::maskMoney($return) : $return;
    }


    function branch()
    {
        return $this->hasOne('App\Branch', 'id', 'branch_id');
    }

    function member()
    {
        return $this->hasOne('App\Member', 'member_id', 'member_id');
    }

    function cashier()
    {
        return $this->hasOne('App\User', 'id', 'cashier_user_id');
    }

    function statusChanger()
    {
        return $this->hasOne('App\User', 'id', 'status_changed_by');
    }

    function rentingDatas()
    {
        return $this->hasMany('App\RentingData', 'transaction_id', 'id');
    }

    function nextPayments()
    {
        return $this->hasMany('App\NextPayment', 'header_id', 'id');
    }

    function isDebt()
    {
        return $this->is_debt && $this->last_payment_date == null;
    }

    function grandTotal()
    {
        return $this->grand_total_item_price-$this->total_item_discount-$this->discount_total_fixed_value + $this->others;
    }

    function log()
    {
        return $this->hasOne('App\Log', 'id', 'log_status_change');
    }


    function paymentStatus($for_view=false)
    {
        if($this->status==3) {
            if($this->log)
            {
                return "Transaksi dihapus.<br/><i style='font-weight: bold;'><u>Note/Reason:</u> ".$this->log->log_text."</i>";
            }
            if($this->paid_value==0) {
                return "Transaksi dihapus sebelum selesai pembayaran";
            }
            return "Transaksi dihapus setelah dinyatakan selesai pembayaran";
        }

        if($this->status==4) {
            $return = "Transaksi dibatalkan, <br/>yang sudah dibayar: ".$this->paidValue($for_view);
            if($this->log)
            {
                $return .= "<br/><i style='font-weight: bold;'><u>Note/Reason:</u> ".$this->log->log_text."</i>";
            }
            return $return;
        }

        if(!$this->is_debt) {
            return "Lunas";
        }
        else {
            if($this->last_payment_date == null) {
                return "Cicilan Belum Lunas";
            }
            return "Cicilan Sudah Lunas";
        }
    }
}
