@extends('master')
<?php /*
@section('optional_css')
@endsection

@section('optional_js')
@endsection
*/ ?>

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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
