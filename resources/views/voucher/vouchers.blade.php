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
                <a href="{{route('get.discount.vouchers',[
                    'page'=>1
                ])}}">Semua Vocuher</a>
            </div>
        </div>
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-vimeo font-purple-rev"></i>
                    @if($voucher_header)
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Voucher{{' '.$voucher_header->voucher_name.' #'.$voucher_header->id}}</span>
                    @else
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Voucher</span>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-info">
                    {{ $message }}
                    {!! HelperService::generatePaging(request()->page, $total_page) !!}
                </div>
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Nama Voucher</th>
                                <th scope="col">Kode Voucher</th>
                                <th scope="col">Status Klaim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discount_vouchers as $voucher)
                                <tr>
                                    <td>{{$voucher->voucherHeader->voucher_name}}
                                        <a href="{{route('get.discount.vouchers',[
                                            'page' => 1,
                                            'batch' => $voucher->voucherHeader->id
                                        ])}}">#{{$voucher->voucherHeader->id}}</a>
                                    </td>
                                    <td>{{ $voucher->voucher_code }}</td>
                                    <td>{{ $voucher->claimed_at ? 'Iya' : 'Belum' }}</td>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
