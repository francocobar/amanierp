@extends('cashier.v2.master')

@section('content')
{!! Form::open(['id' => 'form_finish_trans', 'route' => 'do.cashier.last-step','class'=>'form-horizontal', 'style'=>'width: 50%; margin: 0 auto;']) !!}
<h3 style="text-align: center; font-weight: bold;">Pembayaran Order ID #{{$header->id}}</h3>
<div style="text-align: center; margin-bottom: 10px;">
    <b>Total: {{$header->totalTransaction(true)}}</b>
</div>
<div style="margin-bottom: 10px;">
    <label class="mt-checkbox">
        <input id="lunas" type="checkbox" value="1" name="lunas" checked> Lunas
        <span></span>
    </label>
    <select id="payment_type" class="form-control" name="payment_type">
        <option value="">* Pilih Tipe Pembayaran</option>
            <option value="1">Tunai</option>
            <option value="3">Credit Card</option>
            <option value="4">Debit Card</option>
        </option>
    </select>
</div>
<div style="margin-bottom: 10px;">
    <label>Total yang ingin dibayarkan</label>
    <input type="text" id="paid_value" name="paid_value" value="{{$header->totalTransaction(true)}}" class="mask-money form-control" disabled/>
</div>
<div style="margin-bottom: 10px;">
    <label>Total yang dibayarkan</label>
    <input type="text" id="total_paid" name="total_paid" class="mask-money form-control" />
</div>
<div style="text-align: center; margin-bottom: 10px;">
    <input type="hidden" value="{{$header->id}}" id="ongoing_trans_id" name="ongoing_trans_id" />
    <input type="hidden" name="payment" value="{{Crypt::encryptString('Payment-'.$header->id)}}" />
    <b>Kembalian: <span id="change">0</span></b>
</div>
<div style="margin-bottom: 10px; text-align: center;">
    <input id="btn_finish" type="button" value="Selesai" class="btn btn-success" />
    @if($header->antar_cabang)

    <input id="btn_finish_with_skip" type="button" value="Selesai dengan belum ada pembayaran" class="btn btn-warning" />
    @endif
</div>
<div class="table-scrollable" style="margin-bottom: 10px; text-align: center;">
<table class="table table-striped table-bordered table-hover">
    <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Sudah Terpenuhi sebanyak?<br/>(isi 0 jika belum sama sekali)</th>
    </tr>
    @foreach($details as $detail)
    <tr class="vmiddle-center">
        <td style="text-align: left">{{is_numeric($detail->item_id) ? '#' : $detail->item_id}} {{$detail->custom_name ? $detail->custom_name : $detail->itemInfo->item_name}}</td>
        <td>{{$detail->item_qty}}</td>
        <td class="payment-qty-done">
            <input autocomplete="off" value="{{$detail->item_qty}}" min="0" max="{{$detail->item_qty}}" class="form-control js-qty-done" type="number" name="qty_done[{{$detail->id}}]"  />
            <a class="btn red js-set-0">Belum Sama Sekali</a>
        </td>
    </tr>
    @endforeach
</table>
</div>
@if($header->totalIdrDiscount()>0)
<div style="margin-bottom: 10px;">
    <input type="hidden" id="total_discount" value="{{$header->totalIdrDiscount()}}" />
    <label>
    <b>PENTING</b><br/><br/>
    Terdapat diskon sebesar {{$header->totalIdrDiscount(true)}} pada transaksi ini.<br/>Jika terdapat sebagian
    atau seluruh dari diskon tersebut ditagihkan ke Galeri, tuliskan besarannya pada field berikut.</label>
    <input placeholder="jumlah yang ditagihkan ke galeri" type="text" id="ke_galeri" name="ke_galeri" class="mask-money form-control" />
</div>
<div style="margin-bottom: 10px;">
    Jika anda mengisi kotak diatas, maka setelah Anda menekan tombol <b>Selesai</b> di atas
    akan otomatis membuat invoice tagihan untuk Galeri. List invoice dari cabang Anda
    untuk Galeri dapat dilihat di Aplikasi Kasir pada tombol <b>Tagihan ke Galeri</b>.
</div>
@endif
{!! Form::close() !!}
<input type="hidden" id="total_fix" value="{{$header->totalTransaction()}}" />
@section('blade-script')
<script>
$(document).ready(function(){
    $('.js-set-0').click(function(e){
        e.preventDefault();
        if($(this).parents('td').find('input').prop('readonly')) {
            $(this).parents('td').find('input').prop('readonly', false);
            $(this).parents('td').find('input').val($(this).parents('td').find('input').attr('max'));
        }
        else {
            $(this).parents('td').find('input').prop('readonly', true);
            $(this).parents('td').find('input').val(0);
        }

    });

    $('#btn_finish').click(function(e){

    });
});
</script>
@endsection
@endsection
