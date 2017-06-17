@extends('master')

@if(request()->period == Constant::daily_period)
@section('optional_css')
<link href="{{ URL::asset('css/jquery-ui.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('optional_js')
<script type="text/javascript">
    jQuery(document).ready(function() {
        DatePicker.init();

        $('#date_period').val('{{$report['date_period']}}');
        $('#branch_id').val({{$report['branch']}});
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
    });
</script>
<script src="{{ URL::asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
@endsection
@elseif(request()->period == Constant::monthly_period)
@section('optional_js')
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#month_period').val({{$report['month']}});
        $('#year_period').val({{$report['year']}});
        $('#branch_id').val({{$report['branch']}});
        $('#btn_change_period').click(function(){
            var url = "{{route('get.sales.report',['period'=>'2','spesific'=>'-date-', 'branch'=>'-branch-'])}}";
            url = url.replace("-branch-", $('#branch_id').val());
            location.href= url.replace("-date-", $('#year_period').val()+'-'+$('#month_period').val());
        });
    });
</script>
@endsection
@endif

@section('content')
<div class="row">
    <div class="page-bar" style="margin-top: 10px;">
        @if(request()->period == Constant::daily_period)
        <div class="col-md-2">
            <input id="date_period" type="text" placeholder="{{ $report['period'] }}" class="datepicker form-control" placeholder="Tanggal"/>
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
        <div class="col-md-2">
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
            <a id="btn_change_period"  class="btn purple-rev">Ganti</a>
        </div>
        <div class="page-toolbar">
            <a href="{{env('PRINT_URL').'print-sales-report/'.request()->period.'/'.request()->spesific.'/'.request()->branch}}" id="btn_change_period" class="fa fa-print btn purple-rev" style="padding: 9px;"> Print</a>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true" aria-expanded="true">
                    {{ HelperService::getPeriod(request()->period )}}
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li>
                        <a href="{{route('get.sales.report',['period'=>Constant::daily_period,
                                'spesific' => '0',
                                'branch' => request()->branch
                            ])}}">
                            Harian</a>
                    </li>
                    <li>
                        <a href="{{route('get.sales.report',['period'=>Constant::monthly_period,
                                'spesific' => '0',
                                'branch' => request()->branch
                            ])}}">
                            Bulanan</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Penjualan {{$report['period']}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="header-total" scope="col">Total Jual</th>
                                <th scope="col">Tunai</th>
                                <th scope="col">Non Tunai (Piutang)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ HelperService::maskMoney($report['total_jual']) }}</td>
                                <td>{{ HelperService::maskMoney($report['tunai']) }}</td>
                                <td>{{ HelperService::maskMoney($report['non_tunai']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Omset {{$report['period']}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="header-total" scope="col">Omset</th>
                                <th scope="col">Tunai (dari penjualan)</th>
                                <th scope="col">Pembayaran (Piutang)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ HelperService::maskMoney($report['omset']) }}</td>
                                <td>{{ HelperService::maskMoney($report['tunai']) }}</td>
                                <td>{{ HelperService::maskMoney($report['tunai_piutang']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Rincian Penjualan {{$report['period']}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail)
                            <tr>
                                <td>{{ $detail->itemInfo->item_name }}</td>
                                <td>{{ intval($detail->item_qty_) }}</td>
                                <td class="text-right">{{ HelperService::maskMoney($detail->item_total_price_) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="text-right"colspan="2">
                                    Total [+]
                                </td>
                                <td class="text-right">{{ HelperService::maskMoney($details->sum('item_total_price_')) }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"colspan="2">
                                    Potongan [-]
                                </td>
                                <td class="text-right">{{ HelperService::maskMoney($report['potongan']) }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"colspan="2">
                                    Biaya lain-lain [+]
                                </td>
                                <td class="text-right">{{ HelperService::maskMoney($report['others']) }}</td>
                            </tr>

                            <tr>
                                <td class="header-total text-right"colspan="2">
                                    Netto
                                </td>
                                <td class="text-right">{{ HelperService::maskMoney($report['total_jual']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Rincian Invoice {{$report['period']}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Invoice Id</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $headers_total = 0; ?>
                            @foreach($headers as $header)
                            <tr>
                                <td>{{ $header->invoice_id }}</td>
                                <?php
                                    $total_jual = $header->grand_total_item_price -
                                                $header->total_item_discount - $header->discount_total_fixed_value
                                                + $header->others;
                                    $headers_total += $total_jual;
                                ?>
                                <td class="text-right">{{ HelperService::maskMoney($total_jual) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="header-total text-right">Total Jual</td>
                                <td  class="text-right">{{ HelperService::maskMoney($headers_total) }}</td>
                            </tr>

                            <?php /*
                            <tr>
                                <td class="text-right"colspan="2">
                                    Potongan [-]
                                </td>
                                <td>{{ HelperService::maskMoney($report['potongan']) }}</td>
                            </tr>
                            <tr>
                                <td class="text-right"colspan="2">
                                    Biaya lain-lain [+]
                                </td>
                                <td>{{ HelperService::maskMoney($report['others']) }}</td>
                            </tr>

                            <tr>
                                <td class="header-total text-right"colspan="2">
                                    Netto
                                </td>
                                <td>{{ HelperService::maskMoney($report['total_jual']) }}</td>
                            </tr>
                            */ ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
