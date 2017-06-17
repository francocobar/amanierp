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
                var url = "{{route('get.sales.report.pusat',['period'=>'1','spesific'=>'-date-', 'branch'=>'-branch-'])}}";
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
            var url = "{{route('get.sales.report.pusat',['period'=>'2','spesific'=>'-date-', 'branch'=>'-branch-'])}}";
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
        <div class="col-md-2">
            <select id="branch_id" class="form-control">
                <option value="0">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{$branch->id}}">{{$branch->branch_name.' '.$branch->prefix}}</option>
                @endforeach
            </select>
        </div>
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
                        <a href="{{route('get.sales.report.pusat',['period'=>Constant::daily_period,
                                'spesific' => '0',
                                'branch' => request()->branch
                            ])}}">
                            Harian</a>
                    </li>
                    <li>
                        <a href="{{route('get.sales.report.pusat',['period'=>Constant::monthly_period,
                                'spesific' => '0',
                                'branch' => request()->branch
                            ])}}">
                            Bulanan</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    @if(!isset($report['obj_branch']))
                    <span class="caption-subject font-dark bold uppercase">Penjualan Pusat ke Semua Cabang {{$report['period']}}</span>
                    @else
                    <span class="caption-subject font-dark bold uppercase">Penjualan Pusat ke Cabang {{$report['obj_branch']->branch_name}} {{$report['period']}}</span>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Cabang Pembeli</th>
                                <th scope="col">Ket.</th>
                                <th scope="col">Modal</th>
                                <th scope="col">Omset</th>
                                <th scope="col">Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualan as $penjualan_)
                            <tr>
                                <td>{{$penjualan_->item_id}} {{$penjualan_->itemInfo->item_name}}</td>
                                <td>{{$penjualan_->branch->branch_name}}</td>
                                <td>
                                    @if(empty($penjualan_->description))
                                        -
                                    @else
                                        <a class="bootbox-view-note" data-message="{{$penjualan_->description}}">
                                            <span class="fa fa-eye"></span> Lihat
                                        </a>
                                    @endif
                                </td>
                                <td>{{HelperService::maskMoney(intval($penjualan_->modal))}}</td>
                                <td>{{HelperService::maskMoney(intval($penjualan_->turnover))}}</td>
                                <td>{{HelperService::maskMoney(intval($penjualan_->turnover-$penjualan_->modal))}}</td>
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
