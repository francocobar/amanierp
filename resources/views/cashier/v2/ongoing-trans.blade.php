@extends('cashier.v2.master')

@section('content')
{!! Form::open(['id' => 'form_next_step', 'route' => 'do.cashier.next-step']) !!}
<input type="hidden" value="{{$header->id}}" name="ongoing_trans_id" />
<table class="table table-bordered">
    <thead>
        <tr>
            <td style="text-align: right; font-weight: bold;">
                <input type="hidden" id="trans_set_to" value="" name="trans_set_to" />
                <a href="{{route('get.cashier.v2',$query_string)}}" class="btn">Kembali</a>
                <input data-trans-set-to = "2" type="submit" value="Transaksi Selesai" class="next_step btn btn-success" />
            </td>
        </tr>
    </thead>
</table>
{!! Form::close() !!}

{!! Form::open(['id' => 'form_update_item', 'route' => 'do.cashier.ongoing-update-item']) !!}
<table class="table table-bordered">
    <thead>
        <tr style="font-weight:bold; text-align: right;">
            <td style="width:75%;">
                Order ID: #{{$header->id}}
            </td>
            <td>
                Diskon
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width:75%; text-align: right;">
                {{$header->customer_name}} {{$header->member_id?'#'.$header->member_id : ''}}
            </td>
            <td>
                @if($details->count()!=0)
                <input id="discount" name="discount" type="text" placeholder="diskon" value="{{$discount}}" class="number_only form-control" style="text-align: right; width: 85%; float: left;" />
                <input id="discount_type" name="discount_type" type="text"  class="form-control" style="width:15%; float:left;" value="{{$discount_type}}" />
                @else
                ---
                @endif
            </td>
        </tr>
    </tbody>
</table>

<table class="table table-bordered">
    <thead>
        <tr style="font-weight:bold; text-align: center;">
            <td style="width: 50%;">Item</td>
            <td style="width: 10%;">Qty</td>
            <td style="width: 20%;">Harga/Qty</td>
            <td style="width: 20%;">Sub Total</td>
        </tr>
    </thead>
    <tbody>
        @if($details->count()==0)
        <tr>
            <td colspan="4" style="text-align: center;">Belum ada item</td>
        </tr>
        @else
        <?php $total = 0; ?>
        @foreach($details as $detail)
        <tr>
            <td>
                <span><a class="delete_row">[x]</a> {{$detail->custom_name ? $detail->custom_name : $detail->itemInfo->item_name}}</span>
                <input id="{{$detail->item_id}}" type="hidden" name="item_id[]" value="{{$detail->item_id}}" />
            </td>
            <td>
                <input type="text" data-sub-total="sub_total_{{$detail->item_id}}" data-price="{{$detail->item_price}}" class="number_only item_qty item_qty_already form-control" value="{{$detail->item_qty}}" name="item_qty[]" />
            </td>
            <td style="text-align: right;">{{HelperService::maskMoney($detail->item_price)}}</td>
            <td style="text-align: right;"><span id="sub_total_{{$detail->item_id}}">{{HelperService::maskMoney($detail->item_total_price)}}</span></td>
        </tr>
        <?php $total += $detail->item_total_price ?>
        @endforeach

        <tr>
            <td colspan="3" style="font-weight: bold; text-align: right;">Grand Total</td>
            <td style="text-align: right; font-weight: bold;"><span id="grand_total">{{HelperService::maskMoney($total)}}</span></td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: right;">Potongan</td>
            <td style="text-align: right; font-weight: bold;"><span id="grand_discount">{{HelperService::maskMoney($header->discount_total_fixed_value)}}</span></td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: right;">Grand Total Akhir</td>
            <td style="text-align: right; font-weight: bold;"><span id="grand_total_akhir">{{HelperService::maskMoney($total-$header->discount_total_fixed_value)}}</span></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">
                <input type="hidden" value="{{$header->id}}" name="ongoing_trans_id" />
                <input id="btn_update_item" type="submit" value="Update Item" class="btn btn-success" />
            </td>
        </tr>
        @endif
    </tbody>
</table>
{!! Form::close() !!}

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
                <input id="btn_add_item" type="submit" value="Tambah Item" class="btn btn-success" />
            </td>
        </tr>
    </tbody>
</table>
{!! Form::close() !!}

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
                <input id="add_detail_costumize_item" type="text" class="form-control" name="add_detail_costumize_item" value="" />

            </td>
            <td style="text-align: right;"><input id="add_detail_qty_cos" type="text" class="item_qty3 number_only form-control" value="1" name="add_detail_qty" /></td>
            <td style="text-align: right;"><input id="add_detail_price_cos" type="text" class="mask-money form-control" value="" name="add_detail_price" /></td>
            <td style="text-align: right;"><span id="add_detail_sub_total_price_cos">0</span></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">
                <input type="hidden" value="{{$header->id}}" name="ongoing_trans_id" />
                <input id="btn_add_costumize_item" type="submit" value="Tambah Costumize Item" class="btn btn-success" />
            </td>
        </tr>
    </tbody>
</table>
{!! Form::close() !!}

@if($header->member_id)
<input id="member_trans" type="hidden" value="{{$header->member_id}}" name="ongoing_trans_id" />
@endif
<input type="hidden" id="get_items" value="{{route('get.items.cashier',['branch'=>Crypt::encryptString($branch->id)])}}/" />
@endsection
