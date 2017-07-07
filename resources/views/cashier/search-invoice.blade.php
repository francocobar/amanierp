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

    @if(!empty($keyword))
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Hasil Cari: <i>{{$keyword}}</i></span>
                </div>
            </div>
            <div class="portlet-body">
                @if($headers->count())
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Invoice Id</th>
                                <th scope="col">Cabang</th>
                                <th scope="col">Status Pembayaran</th>
                                <th scope="col">Jumlah Pituang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($headers as $header)
                            <tr>
                                <td><a href="{{route('get.invoice.cashier',['param'=>$header->invoice_id])}}">{{ $header->invoice_id }}</a> |
                                    <a href="{{route('get.invoice.cashier',['param'=>$header->invoice_id, 'detail_klaim'=>1])}}">Detail Klaim</a> |
                                    <a href="{{env('PRINT_URL').str_replace('/','-',$header->invoice_id).'?redirect_back=2'}}">Print Struk</a>
                                    @if($header->rentingDatas->count())
                                     |
                                     <a href="{{route('renting.by.invoice.casier',[
                                        'header_id' => $header->id
                                     ])}}">Data Sewa</a>
                                    @endif
                                </td>
                                <td>{{ $header->branch->branch_name}} </td>
                                <td>{!! $header->isDebt() ? 'Belum Lunas <a href="'.route('get.cashier.next-payment',['invoice'=>str_replace('/','-', $header->invoice_id)]).'">Set Lunas</a>' : 'Lunas' !!}</td>
                                <td>{{ $header->isDebt() ? $header->debtValue(true) : 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    Tidak ada invoice yang invoice id nya (mengandung) <i class="bold">{{$keyword}}</i>.
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
