@extends('master')
@section('optional_css')
<link href="{{ URL::asset('css/jquery-ui.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('optional_js')
<script>
$(document).ready(function(){
    DatePicker.init();
    $('#from').val('{{$from}}');
    $('#to').val('{{$to}}');
    $('#branch_id').val('{{$branch_id}}');
    $('#btn_change_period').click(function(){
        var url = "{{route('topitems.report',['to'=>'-to-', 'from'=>'-from-', 'branch'=>'-branch-'])}}";
        url = url.replace("-branch-", $('#branch_id').val());
        var from = $.trim($('#from').val());
        var to = $.trim($('#to').val());
        if(from.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/) && to.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/)) {
            froms = from.split('-');
            url = url.replace("-from-", froms[2]+'-'+froms[1]+'-'+froms[0]);
            tos = to.split('-');
            url = url.replace("-to-", tos[2]+'-'+tos[1]+'-'+tos[0]);
            location.href= url;
        }
        else {
            alert('Tanggal Invalid');
        }

    });
    $('#search_field').show(200);
    $.ajax({
		url:$('#form_get_items').attr('action'),
		method:"POST",
		dataType:'JSON',
  	    async: false,
		data:$('#form_get_items').serializeArray(),
        success:function(data){
            $.each(data.data, function($key, $value){
                $this_class = $value.item_id;
                $('.'+$this_class).removeClass('anic');
                $('.'+$this_class).html($value.item_name);

            });
        }
    });
});
</script>
@endsection

@section('content')
<div class="row" style="display: none;" id="search_field">
    <div class="portlet light ">
        <div class="portlet-body">
            <div class="col-md-3">
                Dari Tanggal
            </div>
            <div class="col-md-3">
                Sampai Tanggal
            </div>
        </div>
        <div class="portlet-body">
            <div class="col-md-3">
                <input id="from" type="text" class="datepicker form-control" placeholder="Dari Tanggal"/>
            </div>
            <div class="col-md-3">
                <input id="to" type="text" class="datepicker form-control" placeholder="Sampai Tanggal"/>
            </div>
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
<div class="row" style="display: none;" id="search_field">
    <div class="portlet light ">
        <div class="portlet-body">
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
            {!! Form::open(['id' => 'form_get_items', 'route' => 'get.items.by.ajax', 'class'=>'form-horizontal']) !!}

            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Item ID</th>
                                <th scope="col">Item Name</th>
                                <th scope="col">Qty Selesai / Total</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: right;">
                            <?php $number = 1; ?>
                            @foreach($top_items as $top_item)
                            <tr>
                                <td>{{$number}}</td>
                                <td>{{$top_item->item_id}}
                                    <input type="hidden" value="{{$top_item->item_id}}" name="item_id[]" />
                                </td>
                                <td class='{{$top_item->item_id}} anic'>Loading . . .</td>
                                <td>{{$top_item->qty_done}} / {{$top_item->qty}}</td>
                            </tr>
                                <?php $number = $number+1; ?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection
