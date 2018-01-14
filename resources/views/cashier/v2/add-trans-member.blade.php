@extends('cashier.v2.master')

@section('content')
<h2>Tambah Transaksi Member | <a class="btn btn-default" href="{{route('get.cashier.v2',['branch'=>$branch->id])}}">< Kembali</a></h2>
<div class="ui-widget">
    <input id="member" type="text" class="form-control" placeholder="* Nama / Id Member"/>
    <div id="panel_confirmation" class="panel panel-default" style="margin-top: 10px; display: none;">
        <!-- Default panel contents -->
        <div class="panel-heading">Apakah ini member yang anda maksud?</div>
        <!-- Table -->
        <table class="table" style="margin: 0 auto; width: 90%;">
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Nama</th>
                    <th>Nomor Hp</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span id="member_selected"></span></td>
                    <td><span id="member_selected_name"></span></td>
                    <td><span id="member_selected_phone"></span></td>
                    <td><span id="member_selected_address"></span></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center; font-size: 140%;">
                        <span id="reset" class="label label-danger" style="cursor: pointer; font-weight: bold;">Bukan, Ganti</span>
                        <span id="btn_add_trans_member" class="label label-success" style="cursor: pointer; font-weight: bold;">Ya, Lanjutkan</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        {!! Form::open(['id' => 'form_add_trans', 'route' => 'do.cashier.add-transaction']) !!}
                            @if(UserService::isSuperadmin())
                            <input type="hidden" name="add_trans_branch" id="add_trans_branch" value="{{Crypt::encryptString($branch->id)}}"/>
                            @endif
                            <input type="hidden" name="add_trans_type" id="add_trans_type" value="1"/>
                            <input type="hidden" name="add_trans_member" id="add_trans_member" value=""/>
                            <input type="hidden" name="add_trans_parse" value="{{Crypt::encryptString(Sentinel::getUser()->first_name.'-'.$branch->id)}}" />
                        {!! Form::close() !!}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<input type="hidden" id="get_members" value="{{route('get.members.cashier')}}" />
@endsection
