@extends('master')
@section('optional_css')
<link href="{{ URL::asset('css/jquery-ui.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('optional_js')
<script type="text/javascript">
jQuery(document).ready(function() {
    DatePicker.init();
    $('#start_date').val('{{request()->s == '4' ? '' : $var['date_view']}}');
    $('#status').val({{request()->s}});

    $('#status').change(function(){
            if($(this).val() == '4' || $(this).val() == '5') {
                $('#start_date').hide();
            }
            else {
                $('#start_date').show();
            }
    });

    $('#btn_search').click(function(){
        var url_patern = "{{route('renting.by.time.casier',['s'=>'-s-','start_date'=>'-sd-'])}}";

        var sd = $.trim($('#start_date').val());
        url_patern = url_patern.replace('-s-', $('#status').val());
        if($('#status').val()=='4' || $('#status').val()=='5') {
            url_patern = url_patern.replace("start_date=-sd-", '');
        }
        else {
            if($.trim(sd)=='') {
                alert('Pilih Dari Tangal berapa!');
                return;
            }
            if(sd.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/)) {
                sd_ = sd.split('-');
                url_patern = url_patern.replace("-sd-", sd_[2]+'-'+sd_[1]+'-'+sd_[0]);
            }
            else {
                alert('Tanggal tidak valid!');
            }
        }

        location.href=url_patern.replace('amp;','');

    });
});
</script>
@endsection
@section('content')
<div class="row">
    <div class="page-bar" style="margin-top: 10px;">
        <div class="col-md-3">
            <select id="status" class="form-control">
                <option value="1">Segera</option>
                <option value="5">Yang Sudah Lewat</option>
                <option value="2">Sudah Diambil, Belum Dikembalikan</option>
                <option value="3">Sudah dikembalikan</option>
                <option value="4">Denda (>3 hari)</option>
            </select>
        </div>
        <div class="col-md-2">
            <input style="{{in_array(request()->s,['4','5']) ? 'display:none;' : ''}}" id="start_date" type="text" class="datepicker form-control" placeholder="Dari Tanggal"/>
        </div>
        <div class="col-md-1">
            <a id="btn_search"  class="btn purple-rev">Ganti</a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Data Penyewaan</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Cabang Pengambilan</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Tanggal Sewa</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($renting_datas as $renting_data)
                            <tr>
                                <td>{{$renting_data->itemInfo->item_name}}
                                    <a href="{{route('renting.by.invoice.casier',['header_id'=>$renting_data->transaction_id])}}">#{{$renting_data->transaction_id}}</a>
                                </td>
                                <td>{{$renting_data->branch->branch_name}}</td>
                                <td>{{$renting_data->qty}}</td>
                                <td>{{HelperService::inaDate($renting_data->renting_date)}}</td>
                                @if($renting_data->taking_date==null)
                                <td>
                                    Belum diambil
                                </td>
                                @elseif($renting_data->return_date==null)
                                <td>
                                    Sudah diambil,<br/>belum dikembalikan.
                                    <i>{{Carbon\Carbon::parse($renting_data->renting_date)->diffInDays(Carbon\Carbon::today())+1}} hari</i>
                                </td>
                                @else
                                <td>
                                    Sudah dikembalikan.
                                </td>
                                @endif
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
