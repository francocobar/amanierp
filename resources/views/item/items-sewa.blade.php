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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list-ol font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Sewa</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-inline" role="form" style="margin: 10px;">
                    <div class="form-group">
                        <input type="text" class="form-control" id="items_keyword" placeholder="Masukkan Nama Item" value="{{request()->name}}">
                    </div>
                    <a href="{{route('get.items.sewa',['page'=>1,'name'=>'-keyword-'])}}" id='btn_search_items' class="btn btn-success">Cari</a>
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
                                <th scope="col" style="width:250px !important">Id Item Sewa</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Harga Member</th>
                                <th scope="col">Harga Umum</th>
                                <th scope="col">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items_sewa as $item_sewa)
                                <tr>
                                    <td>{{ $item_sewa->item_id }}</td>
                                    <td>
                                        {{ $item_sewa->item_name }}
                                    </td>
                                    <td>{{ HelperService::maskMoney($item_sewa->m_price) }}</td>
                                    <td>{{ HelperService::maskMoney($item_sewa->nm_price) }} </td>
                                    <td align="center">
                                        <a  target="_blank" href="{{route('detail.item.sewa',[
                                                'item_id' => $item_sewa->item_id
                                            ])}}">Edit</a> |
                                        <a target="_blank" href="{{route('input.stock.item',[
                                                'item_id' => $item_sewa->item_id
                                            ])}}">Stok Pusat</a> |
                                        <a target="_blank" href="{{route('supply.stock.item',[
                                                'item_id' => $item_sewa->item_id,
                                                'branch_id' => 'choose_branch'
                                            ])}}">Supply Ke Cabang</a>
                                    </td>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width:250px !important">Id Item Sewa</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Harga Member</th>
                                <th scope="col">Harga Umum</th>
                                <th scope="col">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items_sewa as $item_sewa)
                            <tr>
                                <td>{{ $item_sewa->item_id }}</td>
                                <td>
                                    {{ $item_sewa->item_name }}
                                </td>
                                <td>{{ HelperService::maskMoney($item_sewa->m_price) }}</td>
                                <td>{{ HelperService::maskMoney($item_sewa->nm_price) }}</td>
                                <td>{{ $item_sewa->branchStock == null ? '0' : $item_sewa->branchStock->stock}} </td>
                            </tr>
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
