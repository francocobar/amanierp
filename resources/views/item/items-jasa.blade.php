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
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Jasa</span>
                </div>
            </div>
            <div class="portlet-body">
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
                            @foreach($items_jasa as $item_jasa)
                                <tr>
                                    <td>{{ $item_jasa->item_id }}</td>
                                    <td>
                                        {{ $item_jasa->item_name }}
                                    </td>
                                    <td>{{ HelperService::maskMoney($item_jasa->m_price) }}</td>
                                    <td>{{ HelperService::maskMoney($item_jasa->nm_price) }} </td>
                                    <td align="center">
                                        <a target="_blank" href="{{route('detail.item.jasa',[
                                                'item_id' => $item_jasa->item_id
                                            ])}}">Edit</a>
                                        |
                                        <a target="_blank" href="{{route('configure.item.jasa',[
                                                'item_id' => $item_jasa->item_id
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
                            @foreach($items_jasa as $item_jasa)
                                <tr>
                                    <td>{{ $item_jasa->item_id }}</td>
                                    <td>
                                        {{ $item_jasa->item_name }}
                                    </td>
                                    <td>{{ HelperService::maskMoney($item_jasa->m_price) }}</td>
                                    <td>{{ HelperService::maskMoney($item_jasa->nm_price) }} </td>
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
