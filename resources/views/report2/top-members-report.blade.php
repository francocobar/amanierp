@extends('master')

@section('optional_js')
<script>
$(document).ready(function(){
    $('#month_period').val({{$month}});
    $('#year_period').val({{$year}});
    $('#branch_id').val({{$branch_id}});
    $('#btn_change_period').click(function(){
        var url = "{{route('top.members.report',['spesific'=>'-date-', 'branch'=>'-branch-'])}}";
        url = url.replace("-branch-", $('#branch_id').val());
        location.href= url.replace("-date-", $('#year_period').val()+'-'+$('#month_period').val());
    });
    $('#search_field').show(1000);
    $.ajax({
		url:$('#form_get_members').attr('action'),
		method:"POST",
		dataType:'JSON',
  	    async: false,
		data:$('#form_get_members').serializeArray(),
        success:function(data){
            $.each(data.data, function($key, $value){
                $this_class = $value.member_id.replace(" ", "")
                $('.'+$this_class).removeClass('anic');
                $('.'+$this_class).html($value.full_name);

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
            {!! Form::open(['id' => 'form_get_members', 'route' => 'get.members.by.ajax', 'class'=>'form-horizontal']) !!}

            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Member ID</th>
                                <th scope="col">Nama Member</th>
                                <th scope="col">Total Transaksi (Rupiah)</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: right;">
                            <?php $number = 1; ?>
                            @foreach($top_members as $top_member)
                            <tr>
                                <td>{{$number}}</td>
                                <td>{{$top_member->member_id}}
                                    <input type="hidden" value="{{$top_member->member_id}}" name="member_id[]" />
                                </td>
                                <td class='{{str_replace(' ','',$top_member->member_id)}} anic'>Loading . . .</td>
                                <td>{{HelperService::maskMoney($top_member->total_trans)}}</td>
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
