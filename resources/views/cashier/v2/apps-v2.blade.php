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
    <h2 id="add_trans" style="text-align: center;"><a>Buat Transaksi</a></h2>
</div>
{!! Form::open(['id' => 'form_add_trans', 'route' => 'do.cashier.add-transaction']) !!}
    <input type="hidden" name="add_trans_type" id="add_trans_type" value=""/>
    @if(UserService::isSuperadmin())
    <input type="hidden" name="add_trans_branch" id="add_trans_branch" value="{{Crypt::encryptString($branch->id)}}" />
    @endif
    <input type="hidden" name="add_trans_parse" value="{{Crypt::encryptString(Sentinel::getUser()->first_name.'-'.$branch->id)}}" />
{!! Form::close() !!}
@endsection
