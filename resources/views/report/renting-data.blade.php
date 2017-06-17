@extends('master')

@section('optional_js')
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#btn_search_invoice').click(function(){
            $('.general-error').html('');
            if($.trim($('#keyword').val()) == '') {
                $('.general-error').html('Isi Invoice Id');
                return;
            }
            var url = "{{route('search.invoice.cashier')}}?invoice=-keyword-";
            location.href= url.replace("-keyword-", $.trim($('#keyword').val()));
        });

        $("form").submit(function(e){
            e.preventDefault();
            $('#btn_search_invoice').trigger('click');
        });
        // Bootbox.init();
    });
</script>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Cari Invoice</span>
                </div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['id'=>'invoice_id', 'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Invoice Id
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="keyword" type="text" class="form-control" name="keyword" /> </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9 general-error">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <a id="btn_search_invoice" class="btn purple-rev">Cari Invoice</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Data Penyewaan: {{$header->invoice_id}}</i></span>
                    <span>{{HelperService::inaDate($header->created_at,2)}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="col-xs-4">
                    <b>Status Pembayaran per {{HelperService::inaDate($today,2)}}</b><br/>
                    {{$header->paymentStatus()}}
                </div>
                <div class="col-xs-4">
                    <b>Total Bayar</b><br/>
                    {{$header->paidValue(true)}}
                </div>
                <div class="col-xs-4">
                    <b>Sisa Bayar</b><br/>
                    {{$header->debtValue(true)}}
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Cabang Pengambilan</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Tanggal Sewa</th>
                                <th scope="col">Status</th>
                                <th scope="col">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!$header->isDebt())
                                @foreach($header->rentingDatas as $renting_data)
                                <tr>
                                    <td>{{$renting_data->itemInfo->item_name}}</td>
                                    <td>{{$renting_data->branch->branch_name}}</td>
                                    <td>{{$renting_data->qty}}</td>
                                    <td>{{HelperService::inaDate($renting_data->renting_date)}}</td>

                                    @if($renting_data->taking_date==null)
                                    <td>
                                        Belum diambil
                                    </td>
                                    <td>
                                        @if($is_superadmin || ($employee != null && $employee->branch_id == $renting_data->renting_branch))
                                        <a data-message="Anda yakin {{$renting_data->itemInfo->item_name}} sebanyak {{$renting_data->qty}} telah diambil?" class="bootbox-confirmation" href="{{route('change.status.renting',[
                                            'action'=>'taking',
                                            'renting_data_id'=>Crypt::encryptString($renting_data->id)
                                        ])}}">Diambil</a>
                                        @else
                                            <i>Beda Cabang</i>
                                        @endif
                                    </td>
                                    @elseif($renting_data->return_date==null)
                                    <td>
                                        Sudah diambil,<br/>belum dikembalikan.
                                    </td>
                                    <td>
                                        @if($is_superadmin || ($employee != null && $employee->branch_id == $renting_data->renting_branch))
                                        <a data-message="Anda yakin {{$renting_data->itemInfo->item_name}} sebanyak {{$renting_data->qty}} telah dikembalikan?" class="bootbox-confirmation" href="{{route('change.status.renting',[
                                            'action'=>'return',
                                            'renting_data_id'=>Crypt::encryptString($renting_data->id)
                                        ])}}">Dikembalikan</a>
                                        @else
                                            <i>Beda Cabang</i>
                                        @endif
                                    </td>
                                    @else
                                    <td>
                                        Sudah dikembalikan.
                                    </td>
                                    <td>
                                        &nbsp;
                                    </td>
                                    @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            @foreach($header->rentingDatas as $renting_data)
                            <tr>
                                <td>{{$renting_data->itemInfo->item_name}}</td>
                                <td>{{$renting_data->branch->branch_name}}</td>
                                <td>{{$renting_data->qty}}</td>
                                <td>{{HelperService::inaDate($renting_data->renting_date)}}</td>

                                @if($renting_data->taking_date==null)
                                <td>
                                    Belum diambil
                                </td>
                                <td>
                                    <i>Belum Lunas.</i>
                                </td>
                                @elseif($renting_data->return_date==null)
                                <td>
                                    Sudah diambil,<br/>belum dikembalikan.
                                </td>
                                <td>
                                    <i>Belum Lunas</i>
                                </td>
                                @else
                                <td>
                                    Sudah dikembalikan.
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                                @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
