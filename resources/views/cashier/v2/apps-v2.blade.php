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
    <h2 id="add_trans"><a class="btn purple-rev">Buat Transaksi</a></h2>
    <h1>Cari Invoice</h1>
    <h2><a href="{{route('search.invoice.cashier',['today'=>Crypt::encrypt(array('branch_id'=>$branch->id, 'still_debt'=>false))])}}" target="_blank" class="btn btn-success">Transaksi Hari Ini</a></h2>
    <h2><a href="{{route('search.invoice.cashier',['ke_galeri'=>Crypt::encrypt(array('branch_id'=>$branch->id))])}}" target="_blank" class="btn btn-warning">Tagihan ke Galeri</a></h2>
    <h2><a href="{{route('search.invoice.cashier',['today'=>Crypt::encrypt(array('branch_id'=>$branch->id, 'still_debt'=>true))])}}" target="_blank" class="btn btn-danger">Transaksi Belum Lunas</a></h2>
    <h2><a class="btn btn-success btn-search-invoice" data-for="incentive">Insentif Untuk Karyawan</a></h2>
    <h2><a href="{{route('do.cashier.employee-incentive', ['b'=>Crypt::encryptString($branch->id)]) }}/unset" class="btn btn-warning">Insentif Untuk Karyawan yang beum dibagikan</a></h2>
    <h2><a class="btn btn-success btn-search-invoice" data-for="pending">Cek / Update Transaksi Pending</a></h2>
    @if(UserService::isSuperadmin())
    <h1>Superadmin</h1>
    <h2><a href="{{route('search.invoice.cashier',['ke_galeri'=>Crypt::encrypt(array('branch_id'=>0))])}}" target="_blank" class="btn btn-warning">Tagihan ke Galeri dari Semua Cabang</a></h2>
    <h2><a href="{{route('search.invoice.cashier',['today'=>Crypt::encrypt(array('branch_id'=>0, 'still_debt'=>true))])}}" target="_blank" class="btn btn-danger">Transaksi Belum Lunas Semua Cabang</a></h2>
    @endif
</div>
{!! Form::open(['id' => 'form_add_trans', 'route' => 'do.cashier.add-transaction']) !!}
    <input type="hidden" name="add_trans_type" id="add_trans_type" value=""/>
    @if(UserService::isSuperadmin())
    <input type="hidden" name="add_trans_branch" id="add_trans_branch" value="{{Crypt::encryptString($branch->id)}}" />
    @endif
    <input type="hidden" name="add_trans_parse" value="{{Crypt::encryptString(Sentinel::getUser()->first_name.'-'.$branch->id)}}" />
{!! Form::close() !!}
<input type="hidden" value="{{route('do.cashier.employee-incentive', ['b'=>Crypt::encryptString($branch->id)]) }}/" id="url-incentive" />
<input type="hidden" value="{{route('do.cashier.check-pending', ['b'=>Crypt::encryptString($branch->id)]) }}/" id="url-check-pending" />
@endsection
