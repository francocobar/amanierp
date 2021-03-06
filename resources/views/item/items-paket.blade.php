@extends('master')
<?php /*
@section('optional_css')
@endsection

@section('optional_js')
@endsection
*/ ?>

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list-ol font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Paket</span>
                    <div style="margin: 5px;">
                        @if(request()->keyword)
                        <a href="{{route('get.items.paket',['page'=>1])}}">Semua</a> | <a href="{{route('get.items.paket',['page'=>1,'notconfiguredyet'=>1])}}">Belum Dikonfigurasi</a>
                        @else
                            @if(request()->notconfiguredyet)
                            <a href="{{route('get.items.paket',['page'=>1])}}">Semua</a> | <span class="bold">Belum Dikonfigurasi</span>
                            @else
                            <span class="bold">Semua</span> | <a href="{{route('get.items.paket',['page'=>1,'notconfiguredyet'=>1])}}">Belum Dikonfigurasi</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-inline" role="form" style="margin: 10px;">
                    <div class="form-group">
                        <input type="text" class="form-control" id="items_keyword" placeholder="Masukkan Nama Item" value="{{request()->name}}">
                    </div>
                    <a href="{{route('get.items.paket',['page'=>1,'name'=>'-keyword-'])}}" id='btn_search_items' class="btn btn-success">Cari</a>
                </div>
                <div class="alert alert-info">
                    {{ $message }}
                    {!! HelperService::generatePaging(request()->page, $total_page) !!}
                </div>
                <div class="table-scrollable">

                    @if(strtolower($role_user->slug) == 'superadmin')
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width:250px !important">Id Item Jasa</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Harga Member</th>
                                <th scope="col">Harga Umum</th>
                                <th scope="col">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items_paket as $item_paket)
                                <tr>
                                    <td>{{ $item_paket->item_id }}</td>
                                    <td>
                                        {{ $item_paket->item_name }}
                                    </td>
                                    <td>{{ HelperService::maskMoney($item_paket->m_price) }}</td>
                                    <td>{{ HelperService::maskMoney($item_paket->nm_price) }} </td>
                                    <td align="center">
                                        <a target="_blank" href="{{route('detail.item.paket',[
                                                'item_id' => $item_paket->item_id
                                            ])}}">Edit</a>
                                        |
                                        <a target="_blank" href="{{route('configure.item.paket',[
                                                'item_id' => $item_paket->item_id
                                            ])}}">Konfigurasi</a></td>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width:250px !important">Id Item Jasa</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Harga Member</th>
                                <th scope="col">Harga Umum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items_paket as $item_paket)
                                <tr>
                                    <td>{{ $item_paket->item_id }}</td>
                                    <td>
                                        {{ $item_paket->item_name }}
                                    </td>
                                    <td>{{ HelperService::maskMoney($item_paket->m_price) }}</td>
                                    <td>{{ HelperService::maskMoney($item_paket->nm_price) }} </td>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
