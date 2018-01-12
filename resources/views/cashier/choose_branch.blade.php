
@extends('master')

@section('optional_css')
@endsection

@section('optional_js')
<script type="text/javascript">

$(document).ready(function() {
    $('#btn_go').click(function(e){
        e.preventDefault();
        if($.trim($('#branch_id').val()) == '') {
            alert('Pilih Cabang!');
            return;
        }
        location.href = '/cashier-v2?branch=' + $.trim($('#branch_id').val());
    });
});
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-child font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Pilih Cabang</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Cabang
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>

                                    <select id="branch_id" class="form-control" name="branch">
                                        <option value="">Pilih Cabang</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <button id="btn_go" type="submit" class="btn purple-rev">Pilih Cabang</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
@endsection
