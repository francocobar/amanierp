@extends('cashier.v2.master')

@section('content')
<div class="col-md-6">
    <h2 style="text-align: center;">Transaksi berjalan</h2>
    @if($transaction_ongoing->count())
    <div class="list-group">
        @foreach($transaction_ongoing as $ongoing)
        <a href="{{route('get.cashier.ongoing',['trans_id'=>$ongoing->id])}}" class="list-group-item pointer" style="cursor: pointer;">
            {{$ongoing->customer_name}} <span class="badge">#{{$ongoing->id}}</span>
        </a>
        @endforeach
    </div>
    @else
    <div style="text-align: center">Tidak ada.</div>
    @endif
</div>
<div class="col-md-6">
    <h2 id="add_trans" style="text-align: center;"><a class="btn purple-rev" style="font-size: 110%;">Buat Transaksi</a></h2>
    <h2 style="text-align: center;"><a href="{{route('search.invoice.cashier',['today'=>Crypt::encrypt(array('branch_id'=>$branch->id, 'still_debt'=>false))])}}" target="_blank" class="btn btn-success" style="font-size: 110%;">Transaksi Hari Ini</a></h2>
    <h2 style="text-align: center;"><a href="{{route('search.invoice.cashier',['today'=>Crypt::encrypt(array('branch_id'=>$branch->id, 'still_debt'=>true))])}}" target="_blank" class="btn btn-danger" style="font-size: 110%;">Transaksi Belum Lunas</a></h2>
    @if(UserService::isSuperadmin())
    <h2 style="text-align: center;"><a href="{{route('search.invoice.cashier',['today'=>Crypt::encrypt(array('branch_id'=>0, 'still_debt'=>true))])}}" target="_blank" class="btn btn-danger" style="font-size: 110%;">Transaksi Belum Lunas Semua Cabang</a></h2>
    @endif
</div>
{!! Form::open(['id' => 'form_add_trans', 'route' => 'do.cashier.add-transaction']) !!}
    <input type="hidden" name="add_trans_type" id="add_trans_type" value=""/>
    @if(UserService::isSuperadmin())
    <input type="hidden" name="add_trans_branch" id="add_trans_branch" value="{{Crypt::encryptString($branch->id)}}" />
    @endif
    <input type="hidden" name="add_trans_parse" value="{{Crypt::encryptString(Sentinel::getUser()->first_name.'-'.$branch->id)}}" />
{!! Form::close() !!}
@endsection
