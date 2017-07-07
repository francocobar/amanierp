@extends('master')

@if(request()->period == Constant::daily_period)
@section('optional_css')
<link href="{{ URL::asset('css/jquery-ui.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('optional_js')
@endsection
@elseif(request()->period == Constant::monthly_period)
@section('optional_js')

@endsection
@endif

@section('content')
<div class="row">
<div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Pembukuan {!!$branch_name!!}</span>
                </div>
            </div>
            <div class="portlet-body">
                Total Modal: {{HelperService::maskMoney($pb_->sum('modal_total'), true)}}<br/>
                Total Omset: {{HelperService::maskMoney($pb_->sum('turnover'), true)}}</br>
                Total Profit/Rugi: {{HelperService::maskMoney($pb_->sum('profit'), true)}}
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No. Rekor</th>
                                <th scope="col">Header Id</th>
                                <th scope="col">Detail Id</th>
                                <th scope="col">Cabang</th>
                                <th scope="col">Item</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Modal</th>
                                <th scope="col">Omset</th>
                                <th scope="col">Profit/Rugi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pb_ as $pb)
                            <tr>
                                <td>{{$pb->id}}</td>
                                <td>{!!$pb->header_id ? "<a href='".route('pb.report',[
                                        'header' => $pb->header_id,
                                    ])."'>".$pb->header_id."</a>"  : '-'!!}</td>
                                <td>{!!$pb->detail_id ? "<a href='".route('pb.report',[
                                        'detail' => $pb->detail_id,
                                    ])."'>".$pb->detail_id."</a>"  : '-'!!}</td>
                                <td>{{$pb->branch->branch_name}}</td>
                                <td>{{$pb->itemInfo ? $pb->itemInfo->item_name : '-' }}</td>
                                <td>{{$pb->qty_item}}</td>
                                @if(!empty(trim($pb->description)))
                                <td>
                                    <a class="bootbox-view-note" data-message="{{$pb->getDetailModal()}}">
                                        {{HelperService::maskMoney($pb->modal_total, true)}}
                                    </a>
                                </td>
                                @else
                                <td>{{HelperService::maskMoney($pb->modal_total, true)}}</td>
                                @endif
                                @if(!empty(trim($pb->turnover_description)))
                                <td>
                                    <a class="bootbox-view-note" data-message="{{$pb->getDetailTurnoverDesc()}}">
                                        {{HelperService::maskMoney($pb->turnover, true)}}
                                    </a>
                                </td>
                                @else
                                <td>{{HelperService::maskMoney($pb->turnover, true)}}</td>
                                @endif
                                <td>{{HelperService::maskMoney($pb->profit, true)}}</td>
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
