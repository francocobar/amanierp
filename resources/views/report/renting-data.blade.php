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
            var url = "{{route('search.invoice.report')}}?invoice=-keyword-";
            location.href= url.replace("-keyword-", $.trim($('#keyword').val()));
        });

        $("form").submit(function(e){
            e.preventDefault();
            $('#btn_search_invoice').trigger('click');
        });
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
                                <th scope="col">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($header->rentingDatas as $renting_data)
                            <tr>
                                <td>{{$renting_data->itemInfo->item_name}}</td>
                                <td>{{$renting_data->branch->branch_name}}</td>
                                <td>{{$renting_data->qty}}</td>
                                <td>{{HelperService::inaDate($renting_data->renting_date)}}</td>
                                <td>
                                    @if($renting_data->taking_date==null)
                                    Belum diambil
                                    @elseif($renting_data->return_date==null)
                                    Sudah diambil, belum dikembalikan.
                                    @else
                                    Sudah dikembalikan.
                                    @endif
                                </td>
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
