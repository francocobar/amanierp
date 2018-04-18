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

        $('#invoice_id').submit(function(e){
            e.preventDefault();
            $('#btn_search_invoice').trigger('click');
        });

        $('.change_status').click(function(e){
            e.preventDefault();
            $new_status = $(this).attr('data-status-to');
            $header_id = $(this).attr('data-header-id');
            $alasan = '';
            if($(this).attr('data-status-to') == "3") {
                $message = 'Anda yakin ingin menghapus transaksi ini? <br/><b>Konsekuensi menghapus adalah setiap pembayaran pada transaksi ini akan dianggap tidak ada (omset dari transaksi ini mejadi 0).</b>';
                $alasan = 'menghapus';
            }
            else {
                $message = 'Anda yakin ingin membatalkan transaksi ini? <br/><b>Konsekuensi membatalkan adalah piutang pada transaksi ini akan menjadi 0. Yang sudah dibayarkan akan tetap tercatat sebagai omset.</b>';
                $alasan = 'membatalkan';
            }
            $top_msg = 'Harap sebutkan alasan Anda ' + $alasan + ' transaksi ini?';
            $top_msg += '<textarea id="reason_temp" class="bootbox-input bootbox-input-textarea form-control"></textarea>';
            bootbox.confirm({
                message: $top_msg + '<br/>' + $message,
                buttons: {
                    confirm: {
                        label: 'Ya, saya yakin',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'Tidak',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if(result) {
                        $reason_temp = $.trim($('#reason_temp').val());
                        if($reason_temp == '') {
                            alert('Harap isi alasan Anda!');
                             return false;
                        }
                        else {
                            $('#log').val($reason_temp);
                            $('#new_status').val($new_status);
                            $('#header_id').val($header_id);
                            validateForm($('#change_status'));
                        }
                    }
                    else {
                        $('#new_status').val(0);
                        $('#header_id').val(0);
                        $('#log').val('');
                    }
                }
            });
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
                                <th scope="col">Trx Id</th>
                                <th scope="col">Invoice Id</th>
                                <th scope="col">Cabang</th>
                                <th scope="col">Status Pembayaran</th>
                                <th scope="col">Jumlah Pituang</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($headers as $header)
                            <tr>
                                <td>{{$header->id}}</td>
                                <td><a href="{{route('get.invoice.cashier',['param'=>$header->invoice_id])}}">{{ $header->invoice_id }}</a>
                                    <br/>{{ HelperService::inaDate($header->created_at, 2)}}
                                    @if($header->status==3)
                                        <br/>
                                        <span style="background-color: red; color: white">
                                        Transaksi ini dihapus oleh {{$header->statusChanger->first_name.' #'.$header->statusChanger->id}}
                                        </span>
                                    @endif
                                    @if($header->status==4)
                                        <br/>
                                        <span style="background-color: yellow;">
                                        Transaksi ini dibatalkan oleh {{$header->statusChanger->first_name.' #'.$header->statusChanger->id}}
                                        </span>
                                    @endif
                                    <?php /*
                                    <a href="{{route('get.invoice.cashier',['param'=>$header->invoice_id, 'detail_klaim'=>1])}}">Detail Klaim</a> |


                                    @if($header->rentingDatas->count())
                                     |
                                     <a href="{{route('renting.by.invoice.casier',[
                                        'header_id' => $header->id
                                     ])}}">Data Sewa</a>
                                    @endif
                                    */ ?>
                                </td>
                                <td>{{ $header->branch->branch_name}} </td>
                                <td>{!! $header->paymentStatus(true) !!}</td>
                                <td>{{ $header->isDebt() ? $header->debtValue(true) : 0 }}</td>
                                <td>
                                    @if($header->status==2)
                                        <a href="{{env('PRINT_URL').str_replace('/','-',$header->invoice_id).'?redirect_back=2'}}">Print Struk</a>
                                        @if($header->isDebt())
                                         | <a href="{{route('get.cashier.next-payment',['invoice'=>str_replace('/','-', $header->invoice_id)])}}">Pembayaran Berikutnya</a>
                                        @endif
                                        @if($permissionChangeStatus)
                                         | <a data-header-id="{{$header->id}}" href="" class="change_status" data-status-to="4">Batalkan</a>
                                         | <a data-header-id="{{$header->id}}" class="change_status" data-status-to="3">Hapus</a>
                                         @endif
                                    @else
                                        ----
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {!! Form::open(['route' => 'do.changestatus', 'id'=>'change_status','style'=>'diplay: none;'])!!}
                    <input type="hidden" id="new_status" name="new_status" />
                    <input type="hidden" id="header_id" name="header_id" />
                    <input type="hidden" id="log" name="log" />
                {!! Form::close() !!}
                @else
                    @if(request()->today)
                    Belum ada <i class="bold">{{$keyword}}</i>.
                    @elseif(request()->ke_galeri)
                        Tidak ada <i class="bold">{{$keyword}}</i>.
                    @else
                    Tidak ada invoice yang invoice id nya (mengandung) <i class="bold">{{$keyword}}</i>.
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
