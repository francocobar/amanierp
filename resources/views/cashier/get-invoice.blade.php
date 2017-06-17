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
                    <span class="caption-subject font-dark bold uppercase">{{request()->param}}</span>
                    <span>{{HelperService::inaDate($header->created_at,2)}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="col-xs-4">
                    <b>Kasir / Cabang</b><br/>
                    {{'#'.$header->cashier_user_id.' '.$header->cashier->first_name.' '.$header->cashier->last_name}}
                    {{' / '.$header->branch->branch_name}}
                    <hr/><b>Member</b><br/>
                    @if($header->member)
                    {{'#'.$header->member_id.' '.$header->member->full_name}}
                    @else
                        -
                    @endif
                </div>
                <div class="col-xs-4">
                    <b>Status Pembayaran per {{HelperService::inaDate($today,2)}}</b><br/>
                    {{$header->paymentStatus()}}
                </div>
                <div class="col-xs-4">
                    <b>Total Bayar</b><br/>
                    {{$header->paidValue(true)}} <hr/>
                    <b>Sisa Bayar</b><br/>
                    {{$header->debtValue(true)}}
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="uppercase" scope="col">Item</th>
                                <th class="uppercase" scope="col">Qty</th>
                                <th class="uppercase" scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail)
                                <tr>
                                    <td>{{'#'. $detail->item_id.' '.$detail->itemInfo->item_name}} | {{'@'.HelperService::maskMoney($detail->item_price)}}</td>
                                    <td>{{$detail->item_qty}}</td>
                                    <td class="text-right">{{HelperService::maskMoney($detail->item_total_price)}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="bold text-right uppercase" colspan="2">Sub Total [+]</td>
                                <td class="text-right">{{HelperService::maskMoney($header->grand_total_item_price)}}</td>
                            </tr>
                            @if($header->total_item_discount>0)
                            <tr>
                                <td class="bold text-right uppercase" colspan="2">Diskon [-]</td>
                                <td class="text-right">{{HelperService::maskMoney($header->total_item_discount)}}</td>
                            </tr>
                            @endif
                            @if($header->others>0)
                            <tr>
                                <td class="bold text-right uppercase" colspan="2">Lain-lain [+]</td>
                                <td class="text-right">{{HelperService::maskMoney($header->others)}}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="bold text-right uppercase" colspan="2">Grand Total</td>
                                <td class="text-right">{{HelperService::maskMoney($header->grandTotal())}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
