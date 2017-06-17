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
                    <i class="fa fa-history font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Stok Masuk Diterima</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-info">
                    @include('item.confirmation-supply-branch-menu')
                </div>
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col" style="width:100px !important">Kategori</th>
                                <th scope="col" style="width:150px !important">Jumlah Stok Diterima</th>
                                <th scope="col" style="width:150px !important">Approved Oleh</th>
                                <th scope="col">Waktu Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approved_confirmations as $approved_confirmation)
                                <tr>
                                    <td>{{ $approved_confirmation->item_id.' '.$approved_confirmation->item->item_name }}</td>
                                    <td>{{ ucwords(HelperService::itemTypeById($approved_confirmation->item->item_type)) }}</td>
                                    <td>{{ intval($approved_confirmation->approved_stock).' dari '.$approved_confirmation->stock}}</td>
                                    <td>{{ $approved_confirmation->approver->first_name }}</td>
                                    <td>{{ $approved_confirmation->approval_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
