@extends('master')

@section('optional_css')
<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
<style>
ul.ui-autocomplete{
    z-index: 200000 !important;
}
</style>
@endsection

@section('optional_js')
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
        $('#detail').val($(this).attr('data-detail'));
        $('#incentive').val('');
        $('#employee_id').val('');
        $('#item_pic').val('');
        $('#item_pic').prop('disabled', false);
        $('#modal_set_incentive').modal('show');
    });
});
</script>
@endsection


@section('content')
<input type="hidden" id="get_pic" value="{{route('get.pic.cashier')}}" />
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-child font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Insentif Karyawan Belum Dibagikan</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width:450px !important">Invoice Id</th>
                                <th scope="col">Item</th>
                                <th scope="col">Jumlah Insentif</th>
                                <th scope="col">Belum Dibagikan</th>
                                <th scope="col">&nbsp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unset_incentives as $value)
                                <tr>
                                    <td>{{$value->header->invoice_id}}</td>
                                    <td>{{$value->itemInfo->item_name}}</td>
                                    <td>{{HelperService::maskMoney($value->pic_incentive)}}</td>
                                    <?php $maksimum = $value->pic_incentive-$value->employeeIncentives->sum('incentive'); ?>
                                    <td>{{HelperService::maskMoney($maksimum)}}</td>
                                    <td>
                                        <a class="modal_set" data-detail="{{Crypt::encryptString($value->id)}}" data-maximum="{{$maksimum}}">
                                            BAGIKAN
                                        </a>
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


<div class="modal fade" id="modal_set_incentive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    {!! Form::open(['id' => 'form_set_incentive', 'route' => 'set.incentives.employee.do','class'=>'form-horizontal']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modal_confirmation_title">Bagikan Isentif</h4>
        </div>
        <div class="modal-body">
            <div class="bold">Dari <span id='max_incentive'></span> Bagikan sebanyak</div>
            <input type="hidden" value="" name="detail" id="detail" />
            <input id="incentive" name="incentive" type="text" value="" class="form-control" />
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
@endsection
