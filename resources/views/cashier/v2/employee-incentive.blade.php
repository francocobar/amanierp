@extends('cashier.v2.master')

@section('content')
<h2>Insentif Karyawan| <a class="btn btn-default" href="{{route('get.cashier.v2',['branch'=>$branch->id])}}">< Kembali</a></h2>
<div class="ui-widget">
    <h4><b>Invoice Id :</b> {{$header->invoice_id}}</h4>
    <h4><b>Trx Id :</b> {{$header->id}}</h4>
    <div class="table-scrollable" style="margin-bottom: 10px; text-align: center;">
        <table class="table table-striped table-bordered table-hover">
            <tr>
                <th style="width: 40%;">Item</th>
                <th style="width: 100px !important;">Qty (sudah / total)</th>
                <th>Insentif</th>
            </tr>
            @foreach($details as $detail)
            <tr class="big-tr vmiddle-center">
                <td style="text-align: left">{{$detail->item_id}} {{$detail->custom_name ? $detail->custom_name : $detail->itemInfo->item_name}}</td>
                <td>
                    {{$detail->item_qty_done}} / {{$detail->item_qty}}</td>
                <td>
                    @if($detail->employeeIncentives)
                        <ul class="list-group" style="text-align: left">
                            @foreach($detail->employeeIncentives as $emp_incentive)
                            <li class="list-group-item">
                                    @if($emp_incentive->employee_id)
                                        {{HelperService::maskMoney($emp_incentive->incentive)}} dibagikan ke
                                        {{ $emp_incentive->employee->employee_id.' '.$emp_incentive->employee->full_name }} oleh
                                        {{ '#'.$emp_incentive->setBy->id.' '.$emp_incentive->setBy->first_name }}
                                    @elseif($branch->id == $emp_incentive->branch_id)
                                        <a class="btn btn-default">{{HelperService::maskMoney($emp_incentive->incentive)}}</a>
                                        <a data-detail="{{Crypt::encryptString($emp_incentive->id)}}" class="btn btn-success modal_set" data-incentive="{{HelperService::maskMoney($emp_incentive->incentive)}}">Bagikan</a>
                                    @else
                                        <a class="btn btn-default">{{HelperService::maskMoney($emp_incentive->incentive)}}</a> <a class="btn btn-warning">Belum Dibagikan</a>
                                    @endif
                                    | Insentif tanggung jawab cabang {{ $emp_incentive->branch->branch_name }}
                            </li>
                            @endforeach
                        </ul>
                    @else

                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
<div class="modal fade" id="modal_set_incentive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    {!! Form::open(['id' => 'form_set_incentive', 'route' => 'set.incentives.employee.do','class'=>'form-horizontal']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h2 class="modal-title">Bagikan Insentif</h2>
            <h4><b>Invoice Id :</b> {{$header->invoice_id}}</h4>
            <h4><b>Trx Id :</b> {{$header->id}}</h4>
            <h4><b>Item:</b> <span id="item-selected"></span></h4>
        </div>
        <div class="modal-body">
            <div class="bold">Bagikan sebanyak</div>
            <input type="hidden" value="" name="detail" id="detail" />
            <input id="incentive" name="incentive" type="text" value="" class="form-control" readonly />
            <div class="bold">Kepada</div>
            <input type="hidden" value="" name="employee_id" id="employee_id" />
            <input id="item_pic" name="item_pic" type="text" fvalue="" class="form-control" placeholder="Ketik Nama / Id Karyawan" />
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <a data-form-id="#form_set_incentive" id="btn_set" class="submit-button-validation btn btn-primary">Bagikan</a>
        </div>
    {!! Form::close() !!}
    </div>
  </div>
</div>
<input type="hidden" id="get_pic" value="{{route('get.pic.cashier')}}" />
@endsection

@section('blade-script')
<script type="text/javascript">
$(function() {
    var cache2 = {};
    $('#item_pic').autocomplete({
        minLength: 3,

        source: function(request, response) {
            var term2 = request.term;
            if (term2 in cache2) {
                response(cache2[term2]);
                return;
            }

            $.getJSON($('#get_pic').val(), request, function( data, status, xhr ) {
                    cache2[term2] = data;
                    response( data );
                });
        },
        focus: function(event, data) {
            return false;
        },
        select: function( event, ui ) {
            $('#item_pic').val(ui.item.employee_id+ ' ' +ui.item.full_name);
            $('#item_pic').prop('disabled', true);
            $('#employee_id').val(ui.item.employee_id);
            return false;
        }
    })
    .autocomplete("instance")._renderItem = function( ul, data ) {
        return $("<li>")
        .append("<div><b>" + data.employee_id + "</b> | " + data.full_name + "</div>")
        .appendTo( ul );
    };
});

jQuery(document).ready(function() {
    $('.modal_set').click(function(){
        $('#max_incentive').text(maskMoney($(this).attr('data-maximum')));
        $item = $(this).parents('.big-tr').find('td:first-child').html();
        // console.log($item);
        $('#item-selected').html($item);
        $('#detail').val($(this).attr('data-detail'));
        $('#incentive').val($(this).attr('data-incentive'));
        $('#employee_id').val('');
        $('#item_pic').val('');
        $('#item_pic').prop('disabled', false);
        $('#modal_set_incentive').modal('show');
    });
});
</script>
@endsection

@section('blade-style')
<style>
ul.ui-autocomplete{
    z-index: 200000 !important;
}
</style>
@endsection
