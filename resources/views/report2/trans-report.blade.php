@extends('master')

@if(request()->period == Constant::daily_period)
@section('optional_css')
<link href="{{ URL::asset('css/jquery-ui.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('optional_js')
<script type="text/javascript">
    jQuery(document).ready(function() {
        DatePicker.init();
        $('#date_period').val('{{$date_period}}');
        $('#branch_id').val('{{$branch_id}}');
        $('#btn_change_period').click(function(){
            if($.trim($('#date_period').val()) == '') {
                alert('Pilih tanggal!');
                return;
            }
            var period = $.trim($('#date_period').val());

            if(period.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/)) {
                var url = "{{route('get.sales.report',['period'=>'1','spesific'=>'-date-', 'branch'=>'-branch-'])}}";
                url = url.replace("-branch-", $('#branch_id').val());
                periods = period.split('-');
                location.href= url.replace("-date-", periods[2]+'-'+periods[1]+'-'+periods[0]);
            }
            else {
                alert('Tanggal tidak valid!');
            }
        });

        $('#search_field').show(1000);
    });
</script>
<script src="{{ URL::asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
@endsection
@elseif(request()->period == Constant::monthly_period)
@section('optional_js')
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#month_period').val({{$month}});
        $('#year_period').val({{$year}});
        $('#branch_id').val({{$branch_id}});
        $('#btn_change_period').click(function(){
            var url = "{{route('get.sales.report',['period'=>'2','spesific'=>'-date-', 'branch'=>'-branch-'])}}";
            url = url.replace("-branch-", $('#branch_id').val());
            location.href= url.replace("-date-", $('#year_period').val()+'-'+$('#month_period').val());
        });
        $('#search_field').show(1000);
    });
</script>
@endsection
@endif

@section('content')
<div class="row" style="display: none;" id="search_field">
    <div class="portlet light ">
        <div class="portlet-body">
            @if(request()->period == Constant::daily_period)
            <div class="col-md-3">
                <input id="date_period" type="text" class="datepicker form-control" placeholder="Tanggal"/>
            </div>
            @elseif(request()->period == Constant::monthly_period)
            <div class="col-md-2">
                <select id="month_period" class="form-control">
                    @foreach(HelperService::arrayMonth() as $int_month => $month)
                        <option value="{{$int_month}}">{{$month}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="year_period" class="form-control">
                    @for($year=2017; $year<=date('Y'); $year++)
                        <option value="{{$year}}">{{$year}}</option>
                    @endfor
                </select>
            </div>
            @endif
            @if(!session()->has('branch_id'))
            <div class="col-md-3">
                <select id="branch_id" class="form-control">
                    <option value="0">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name.' '.$branch->prefix}}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" id="branch_id" value="{{session('branch_id')}}" />
            @endif
            <div class="col-md-1">
                <a id="btn_change_period"  class="btn purple-rev">Lihat Laporan</a>
            </div>
            <div style="clear: both;">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">{{$title}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Total Transaksi Baru</th>
                                <th scope="col">Total Omset (Transaksi Baru + Pembayaran Cicilan)</th>
                                <th scope="col">Total Piutang Baru</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: right;">
                            <tr>
                                <td>{{HelperService::maskMoney($transaction_total)}}</td>
                                <td>{{HelperService::maskMoney($transaction_turnover)}}</td>
                                <td>{{HelperService::maskMoney($transaction_debt)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Rincian</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr style="text-align: center;">
                                <th>Invoice ID</th>
                                <th>Cabang Transaksi</th>
                                <th>Transaksi</th>
                                <th>Omset</th>
                                <th>Piutang</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: right;">
                            @if($headers->count())
                            @foreach($headers as $header)
                            <tr>
                                <td>Transaksi Baru {{$header->invoice_id}}</td>
                                <td>{{$header->branch->branch_name}}</td>
                                <td>{{$header->totalTransaction(true)}}</td>
                                <td>{{HelperService::maskMoney($header->paid_value)}}</td>
                                <td>{{HelperService::maskMoney($header->debt)}}</td>
                            </tr>
                            @endforeach
                            @endif
                            @if($installments->count())
                            @foreach($installments as $installment)
                            <tr>
                                <td>Pembayaran Cicilan {{$installment->header->invoice_id}}</td>
                                <td>{{$installment->branch->branch_name}}</td>
                                <td>---</td>
                                <td>{{$installment->paidValue(true)}}</td>
                                <td>---</td>
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
