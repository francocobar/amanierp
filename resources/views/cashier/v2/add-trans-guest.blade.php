@extends('cashier.v2.master')

@section('content')
<h2>Tambah Transaksi Guest | <a class="btn btn-default" href="{{route('get.cashier.v2',['branch'=>$branch->id])}}">< Kembali</a></h2>
<div class="ui-widget">
    <div id="panel_confirmation" class="panel panel-default" style="margin-top: 10px;">
        <!-- Default panel contents -->
        {!! Form::open(['id' => 'form_add_trans', 'route' => 'do.cashier.add-transaction']) !!}
        @if(UserService::isSuperadmin())
        <input type="hidden" name="add_trans_branch" id="add_trans_branch" value="{{Crypt::encryptString($branch->id)}}"/>
        @endif
        <input type="hidden" name="add_trans_type" id="add_trans_type" value="2"/>
        <input type="hidden" name="add_trans_parse" value="{{Crypt::encryptString(Sentinel::getUser()->first_name.'-'.$branch->id)}}" />
        <!-- Table -->
        <table class="table" style="margin: 0 auto; width: 90%;">
            <thead>
                <tr>
                    <th>Nama (wajib diisi)</th>
                    <th>Nomor Hp</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input placeholder="Nama Tamu" class="form-control" type="text" name="add_trans_guest_name" id="add_trans_guest_name" value=""/>
                    </td>
                    <td>
                        <input placeholder="No Hp Tamu" class="form-control" type="text" name="add_trans_guest_phone" id="add_trans_guest_phone" value=""/>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center; font-size: 140%;">
                        <span id="reset" class="label label-danger" style="cursor: pointer; font-weight: bold;">Bukan, Ganti</span>
                        <span id="btn_add_trans_guest" class="label label-success" style="cursor: pointer; font-weight: bold;">Ya, Lanjutkan</span>
                    </td>
                </tr>
            </tbody>
        </table>

        {!! Form::close() !!}
    </div>
</div>

<input type="hidden" id="guest" value="1" />
@endsection
