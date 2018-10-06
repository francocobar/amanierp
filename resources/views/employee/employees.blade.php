@extends('master')
<?php /*
@section('optional_css')
@endsection
*/ ?>

@section('optional_js')
<script>
$(document).ready(function(){
    $('.js-set-work-since').click(function(e){
        $employee_id = $(this).attr('data-employee-id');
        $employee_full_name = $(this).attr('data-employee-fullname');
        bootbox.prompt({
            title: "#" + $employee_id + " " +  $employee_full_name + " bekerja sejak:",
            inputType: 'date',
            callback: function (result) {
                if(result) {
                    $('#ws_date').val(result);
                    $('#ws_employee_id').val($employee_id);
                    validateForm($('#ws_form'));
                }
                else {
                    $('#ws_date').val('');
                    $('#ws_employee_id').val('');
                }
            }
        });
    });
});
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-child font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Karyawan</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-info">
                    {{ $message }}
                    {!! HelperService::generatePaging(request()->page, $total_page) !!}
                </div>
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width:450px !important">Id Karyawan</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Cabang</th>
                                <th scope="col">Bekerja Sejak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <a href="{{route('get.employee.user',[
                                                'employee_id'=>$employee->employee_id,
                                                'user_id'=>$employee->user_id
                                            ])
                                        }}">
                                        {{$employee->employee_id}}
                                        </a>
                                    </td>
                                    <td>{{$employee->full_name}}</td>
                                    <td>{{$employee->branch->branch_name}}</td>
                                    <td>{!!$employee->workSince('view') == 'Unset' ? '<a class="js-set-work-since" data-employee-fullname="'.$employee->full_name.'" data-employee-id="'.$employee->employee_id.'">Set Tanggal Mulai Bekerja</a>' : $employee->workSince('view') !!}</td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Form::open(['id' => 'ws_form', 'route' => 'update.employee.work-since.do','class'=>'form-horizontal']) !!}
    <input id="ws_employee_id" type="hidden" class="form-control" value="" name="employee_id" />
    <input id="ws_date" type="hidden" class="form-control" value="" name="work_since" />
{!! Form::close() !!}
@endsection
