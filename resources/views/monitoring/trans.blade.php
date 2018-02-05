@extends('monitoring.master')
@section('optional_js')
<script>
$(document).ready(function(){
    function appendDataCount($datacount)
    {
        $.each($datacount, function(x,y){
            $('#status'+x).html(y);
            $('#status'+x).removeClass('anic');
            $('#status'+x).addClass('anic2');
        });
    }
    function appendTableEachBranch($branches)
    {
        $.each($branches, function(x,branch){
            if($('#branch'+branch.id).length==0) {
                $new_table = $('#temp_table').clone(true);
                $new_table.attr('id', 'branch'+branch.id);
                $new_table.find('.branchname').html(branch.branch_name);
                $new_table.show();
                $('#date_per_branch').append($new_table);
            }
        });
    }
    function appendDataEachBranch($dataeachbranch)
    {
        $.each($dataeachbranch, function(branchid,data){
            $.each(data, function(x,y){
                $('#'+branchid).find('.status'+x).removeClass('anic');
                $('#'+branchid).find('.status'+x).addClass('anic2');
                $('#'+branchid).find('.status'+x).html(y);
            });
        });
    }

    function loadData() {
        ajaxFunc();
        myVar = setInterval(ajaxFunc, 120000);
    }

    function ajaxFunc() {
        $('.anic2').html('Loading . . .');
        $('.anic2').addClass('anic');
        $.ajax({
    		url:"{{route('monitoring.trans')}}",
    		method:"GET",
    		dataType:'JSON',
            success:function(data){
                appendDataCount(data.count);
                appendTableEachBranch(data.branches);
                appendDataEachBranch(data.per);
            }
        });
    }
    $('#refresh').click(function(e){
        e.preventDefault();
        ajaxFunc();
    })
    loadData();

});
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="#">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span id="status1" class="anic">Loading . . .</span>
                </div>
                <div class="desc">Transaksi sedang berjalan</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green" href="#">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span id="status2" class="anic">Loading . . .</span>
                </div>
                <div class="desc">Transaksi selesai</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="#">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span id="status3" class="anic">Loading . . .</span>
                </div>
                <div class="desc">Transaksi dibatalkan</div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="text-align: center;">
        <button id="refresh" class="btn btn-default">REFRESH</button><br/>
        Data akan diperbaharui setiap dua menit. Jika anda ingin meliat data terbaru lebih cepat tekan
        tombol REFRESH di atas.
    </div>
</div>
<div id="date_per_branch" class="row" style="margin-top: 10px;">
    <div id="temp_table" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none;">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 25%;">Nama Cabang</th>
                    <th style="width: 25%;">Transaksi sedang berjalan</th>
                    <th style="width: 25%;">Transaksi selesai</th>
                    <th style="width: 25%;">Transaksi dibatalkan</th>
                </tr>
            </thead>
            <tbody>
                <tr style="text-align: center;">
                    <td class="branchname"> </td>
                    <td class="status1 anic">Loading . . .</td>
                    <td class="status2 anic">Loading . . .</td>
                    <td class="status3 anic">Loading . . .</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
