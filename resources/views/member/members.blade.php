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
                    <i class="fa fa-users font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Member</span>
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
                                <th scope="col" style="width:450px !important">Id Member</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Cabang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td>
                                        <a href="">
                                            {{$member->member_id}}
                                        </a>
                                    </td>
                                    <td>{{$member->full_name}}</td>
                                    <td>{{$member->branch->branch_name}}</td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
