@extends('master')

@section('optional_js')
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">{{'Daftar Insentif Bulan '.$month_selected.' - '.$year_selected}}</span>
                </div>
            </div>
            {!! Form::open(['id' => 'form_get_members', 'route' => 'get.members.by.ajax', 'class'=>'form-horizontal']) !!}

            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Karyawan</th>
                                <th scope="col">Total Insentif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach($incentives as $incentive)
                            <tr>
                                <td>{{$number}}</td>
                                @if($incentive->employee_id)
                                    <td>{{$incentive->employee_id.' '.App\Employee::where('employee_id',$incentive->employee_id)->first()->full_name}}</td>
                                @else
                                    <td><i>Belum dibagikan.</i></td>
                                @endif
                                <td>{{HelperService::maskMoney($incentive->total_incentive)}}</td>
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
