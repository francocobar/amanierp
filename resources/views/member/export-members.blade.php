@extends('master')

@section('optional_css')
@endsection

@section('optional_js')

@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-user-plus font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Export Members</span>
                </div>
            </div>
            <div class="portlet-body" style="margin: 10px;">
                @foreach($branches as $branch)
                    {{$branch->branch_name}}
                    | <a href="{{route('export.members.do', $branch->id)}}">DOWNLOAD EXCEL</a><br/>
                @endforeach
            </div>
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
@endsection
