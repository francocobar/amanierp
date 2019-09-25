@extends('cashier.rev.master')

@section('content')
<?php $number = 1 ?>
{!! Form::open(['id' => 'form_next_step', 'route' => 'do.cashier.next-step']) !!}
<input id="js-flag-trans-id" type="hidden" value="{{$header->id}}" name="ongoing_trans_id" />
<table class="table table-bordered">
    <thead>
        <tr>
            <td style="text-align: right; font-weight: bold;">
                <input type="hidden" id="trans_set_to" value="" name="trans_set_to" />
                <a href="{{route('get.cashier.v2',$query_string)}}" class="btn btn-default">< Kembali</a>
                <input data-trans-set-to = "3" type="submit" value="Batalkan Transaksi" class="next_step btn btn-danger" />
                <input data-trans-set-to = "2" type="submit" value="Transaksi Selesai" class="next_step btn btn-success" />
            </td>
        </tr>
    </thead>
</table>
{!! Form::close() !!}
<div style="border: 3px black solid; margin-bottom: 5px;">
{!! Form::open(['id' => 'form_update_item', 'route' => 'do.cashier.ongoing-update-item']) !!}
<table class="table table-bordered">
    <thead>
        <tr style="font-weight:bold; text-align: right;">
            <td style="width:75%;">
                Order ID: #{{$header->id}}
            </td>
            <td>
                Diskon Total
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width:75%; text-align: right;">
                {{$header->customer_name}} {{$header->member_id?'#'.$header->member_id : ''}}
            </td>
            <td>
                <input id="discount" name="discount" type="text" placeholder="diskon" value="{{$discount}}" class="number_only form-control" style="text-align: right; width: 85%; float: left;" />
                <input id="discount_type" name="discount_type" type="text"  class="form-control" style="width:15%; float:left;" value="{{$discount_type}}" />
            </td>
        </tr>
    </tbody>
</table>

<table class="table table-bordered">
    <thead>
        <tr style="font-weight:bold; text-align: center;">
            <td style="width: 30%;">Item</td>
            <td style="width: 10%;">Qty</td>
            <td style="width: 10%;">Harga/Qty</td>
            <td style="width: 10%;">Sub Total</td>
            <td style="width: 20%;">Diskon Item</td>
            <td style="width: 10%;">Nilai Diskon</td>
            <td style="width: 10%;">Sub Total Akhir</td>
        </tr>
    </thead>
    <tbody id="items_body">
        @if($details->count()==0)
        <tr id="no_item">
            <td colspan="7" style="text-align: center;">Belum ada item</td>
        </tr>
        @else
        @foreach($details as $detail)
        <tr>
            <td>
                <span><a class="delete_row">[x]</a> {{$detail->custom_name ? $detail->custom_name : $detail->itemInfo->item_name}}</span>
                <input id="{{$detail->item_id}}" type="hidden" name="item_id[]" value="{{$detail->item_id}}" />
            </td>
            <td>
                <input type="text" data-discount-type="discount_type_{{$detail->item_id}}" data-discount="discount_{{$detail->item_id}}" data-fixed-sub-total="fixed_sub_total_{{$detail->item_id}}" data-sub-total="sub_total_{{$detail->item_id}}" data-price="{{$detail->item_price}}" class="number_only item_qty item_qty_already form-control" value="{{$detail->item_qty}}" name="item_qty[]" />
            </td>
            <td style="text-align: right;">{{HelperService::maskMoney($detail->item_price)}}</td>
            <td style="text-align: right;"><span id="sub_total_{{$detail->item_id}}">{{HelperService::maskMoney($detail->item_total_price)}}</span></td>
            <td>
                <input id="discount_{{$detail->item_id}}" name="discount_{{$detail->item_id}}" type="text" placeholder="diskon item" value="{{HelperService::maskMoney(intval($detail->item_discount_input))}}" class="item_discount mask-money form-control" style="text-align: right; width: 80%; float: left;" />
                <input id="discount_type_{{$detail->item_id}}" name="discount_type_{{$detail->item_id}}" type="text"  class="item_discount form-control" style="width:20%; float:left;" value="{{$detail->item_discount_type == 2 ? '' : '%'}}" />
            </td>
            <td style="text-align: right;"><span id="val_discount_{{$detail->item_id}}">{{HelperService::maskMoney($detail->item_discount_fixed_value)}}</span></td>
            <td style="text-align: right;"><span id="fixed_sub_total_{{$detail->item_id}}">{{$detail->fixedSubTotal(true)}}</span></td>

        </tr>
        <?php
            if($detail->custom_name) {
                $number++;
            }
        ?>
        @endforeach
        @endif
    </tbody>
</table>
<table class="table table-bordered" style="border-top: 3px dashed black;">
    <tbody id="grand_body">
        <tr>
            <td colspan="3" style="width: 50%; font-weight: bold; text-align: right;">Grand Total</td>
            <td style="width: 10%; font-weight: bold; text-align: right;"><span id="grand_total">{{HelperService::maskMoney($header->grand_total_item_price)}}</span></td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 10%; font-weight: bold; text-align: right;"><span id="total_item_discount">{{HelperService::maskMoney($header->total_item_discount)}}</span></td>
            <td style="width: 10%; text-align: right; font-weight: bold; color: red;"><span id="grand_total_2">{{HelperService::maskMoney($header->grand_total_item_price-$header->total_item_discount)}}</span></td>
        </tr>
        <tr>
            <td colspan="6" style="font-weight: bold; text-align: right;">Potongan</td>
            <td style="text-align: right; font-weight: bold; color: red;"><span id="grand_discount">{{HelperService::maskMoney($header->discount_total_fixed_value)}}</span></td>
        </tr>
        <tr>
            <td colspan="6" style="font-weight: bold; text-align: right;">Grand Total Akhir</td>
            <td style="text-align: right; font-weight: bold; color: red;"><span id="grand_total_akhir">{{$header->totalTransaction(true)}}</span></td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: right;">
                <input type="hidden" value="{{$header->id}}" name="ongoing_trans_id" />
                <?php /*
                <input id="btn_update_item" type="submit" value="Update Item" class="btn btn-success" />
                */ ?>
            </td>
        </tr>
    </tbody>
</table>
<div id="temp">

</div>
{!! Form::close() !!}
</div>

<div style="border: 3px black solid; margin-bottom: 5px;">
{!! Form::open(['id' => 'form_add_item', 'route' => 'do.cashier.ongoing-add-item']) !!}
<table class="table table-bordered">
    <thead>
        <tr style="font-weight:bold;">
            <td style="width: 50%;">Item</td>
            <td style="width: 10%;">Qty</td>
            <td style="width: 20%;">Harga/Qty</td>
            <td style="width: 20%;">Sub Total</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <input id="add_detail_item" type="text" class="form-control" name="add_detail_item" value="" />
                <input id="add_detail_item_id" type="hidden" name="add_detail_item_id" value="" />
                <input id="add_detail_item_price_val" type="hidden" name="add_detail_item_price_val" value="" />

            </td>
            <td style="text-align: right;"><input id="add_detail_qty" type="text" class="item_qty2 number_only form-control" value="1" name="add_detail_qty" /></td>
            <td style="text-align: right;"><span id="add_detail_item_price">0</span></td>
            <td style="text-align: right;"><span id="add_detail_sub_total_price">0</span></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">
                <input type="hidden" value="{{$header->id}}" name="ongoing_trans_id" />
                <input id="reset_add_item" type="button" value="Reset" class="btn btn-default" />
                <input id="btn_add_item" type="submit" value="Tambah Item" class="btn btn-success" />
            </td>
        </tr>
    </tbody>
</table>
{!! Form::close() !!}
</div>

<div style="border: 3px black solid; margin-bottom: 5px;">
{!! Form::open(['id' => 'form_add_costumize_item', 'route' => 'do.cashier.ongoing-add-item']) !!}
<table class="table table-bordered">
    <thead>
        <tr style="font-weight:bold;">
            <td style="width: 50%;">Costumize Item</td>
            <td style="width: 10%;">Qty</td>
            <td style="width: 20%;">Harga/Qty</td>
            <td style="width: 20%;">Sub Total</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <input id="add_detail_item_cos" type="text" class="form-control" name="add_detail_item_cos" value="" />
                <input id="add_detail_item_id_cos" type="hidden" class="form-control" name="add_detail_item_id_cos" value="{{$number}}" />

            </td>
            <td style="text-align: right;"><input id="add_detail_qty_cos" type="text" class="item_qty3 number_only form-control" value="1" name="add_detail_qty" /></td>
            <td style="text-align: right;"><input id="add_detail_price_cos" type="text" class="mask-money form-control" value="" name="add_detail_price" /></td>
            <td style="text-align: right;"><span id="add_detail_sub_total_price_cos">0</span></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">
                <input type="hidden" value="{{$header->id}}" name="ongoing_trans_id" />
                <input id="reset_add_item_cos" type="button" value="Reset" class="btn btn-default" />
                <input id="btn_add_costumize_item" type="submit" value="Tambah Costumize Item" class="btn btn-success" />
            </td>
        </tr>
    </tbody>
</table>
{!! Form::close() !!}
</div>

<table style="display:none;">
    <tr id="tr_temp">
            <td>
                <span><a class="delete_row">[x]</a> --replace_name--</span>
                <input id="--replace_id--" type="hidden" name="item_id[]" value="--replace_id--">
            </td>
            <td>
                <input type="text" data-discount-type="discount_type_--replace_id--" data-discount="discount_--replace_id--" data-fixed-sub-total="fixed_sub_total_--replace_id--" data-sub-total="sub_total_--replace_id--" data-price="--replace_unmask_price--" class="number_only item_qty item_qty_already form-control" value="--replace_qty--" name="item_qty[]">
            </td>
            <td style="text-align: right;">--replace_price_per_qty--</td>
            <td style="text-align: right;"><span id="sub_total_--replace_id--">--replace_sub_total--</span></td>
            <td>
                <input id="discount_--replace_id--" name="discount_--replace_id--" type="text" placeholder="diskon item" value="0" class="item_discount mask-money form-control" style="text-align: right; width: 80%; float: left;">
                <input id="discount_type_--replace_id--" name="discount_type_--replace_id--" type="text" class="item_discount form-control" style="width:20%; float:left;" value="%">
            </td>
            <td style="text-align: right;"><span id="val_discount_--replace_id--">0</span></td>
            <td style="text-align: right;"><span id="fixed_sub_total_--replace_id--">--replace_sub_total--</span></td>
    </tr>
</table>
<input type="hidden" id="hidden_temp" value="" />
@if($header->member_id)
<input id="member_trans" type="hidden" value="{{$header->member_id}}" name="ongoing_trans_id" />
@endif
<input type="hidden" id="get_items" value="{{route('cashier.search-items',['trans_id'=>$header->id,'branch'=>Crypt::encryptString($branch->id)])}}/" />
@endsection

@section('blade-script')
<script src="{{ URL::asset('js/cashier-rev/transactions/item-auto-complete.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/cashier-rev/transactions/add-item.js') }}" type="text/javascript"></script>
@endsection
