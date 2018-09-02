@extends('cashier.v2.master')

@section('content')
<h2>Cek / Update Pending Trans | <a class="btn btn-default" href="{{route('get.cashier.v2',['branch'=>$branch->id])}}">< Kembali</a></h2>
<div class="ui-widget">
    <h4><b>Invoice Id :</b> {{$header->invoice_id}}</h4>
    <h4><b>Trx Id :</b> {{$header->id}}</h4>
    {!! Form::open(['id' => 'form_add_member', 'route' => 'do.cashier.update-pending', 'class'=>'form-horizontal']) !!}
    <input type="hidden" name="params" value="{{encrypt(array('branch_id'=>$branch->id, 'header_id'=>$header->id))}}" />
    <div class="table-scrollable" style="margin-bottom: 10px; text-align: center;">
        <table class="table table-striped table-bordered table-hover">
            <tr>
                <th style="width: 40%;">Item</th>
                <th style="width: 100px !important;">Qty (sudah / total)</th>
                <th>Tambahkan Qty selesai</th>
            </tr>
            @foreach($details as $detail)
            <tr class="big-tr vmiddle-center">
                <td style="text-align: left">{{$detail->item_id}} {{$detail->custom_name ? $detail->custom_name : $detail->itemInfo->item_name}}</td>
                <td>
                    {{$detail->item_qty_done}} / {{$detail->item_qty}}</td>
                <td>
                    @if($detail->item_qty_done < $detail->item_qty)
                    <input class="form-control" style="max-width: 10%; margin: 0 auto;" min="0" max="{{$detail->item_qty-$detail->item_qty_done}}" autocomplete="off" type="number" value="0" name="{{encrypt(array('qty_max'=>$detail->item_qty-$detail->item_qty_done, 'detail_id'=>$detail->id))}}" />
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2">&nbsp;</td>
                <td>
                    <a class="btn btn purple-rev submit-button">Tambah Qty</a>
                </a>
            </tr>
        </table>
    </div>
    {!! Form::close() !!}
</div>
@endsection
