@extends('master')

@section('content')

@if(request()->route()->getName()=='dashboard')
    @include('employee.give-new-password')
@else
    @if(strtolower($role_user_login->slug) == 'superadmin')
    @include('employee.change-role')
    @endif
    @include('employee.give-new-password')
    @if(request()->user_id != null && request()->user_id != Sentinel::getUser()->id)
    @include('employee.salary')
    @endif
@endif
@endsection

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../js/employee-user.js" type="text/javascript"></script>
@endsection
