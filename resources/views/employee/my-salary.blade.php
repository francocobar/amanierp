@extends('master')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-history font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold">Riwayat Gaji Pokok</span>
                </div>
            </div>

            <?php
                $this_month_already_exist = $next_month_already_exist = false;
                $first_of_this_month = Carbon\Carbon::today()->firstOfMonth()->toDateString();
                $first_of_next_month = Carbon\Carbon::today()->addMonth(1)->firstOfMonth()->toDateString();
            ?>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th> Gaji </th>
                            <th> Berlaku Dari </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee_salaries as $salary)
                        <?php
                            $valid_since = HelperService::inaDate($salary->valid_since);
                            $salary_string = HelperService::maskMoney($salary->employee_salary);
                        ?>
                        <tr>
                            <td>{{$salary_string}}
                            </td>
                            <td>{{$valid_since}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-credit-card font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold">Gaji Pokok Bulan Ini: {{
                        $salary_now ? HelperService::maskMoney($salary_now->employee_salary) : '0'}}</span>
                </div>
            </div>

            <?php
                $this_month_already_exist = $next_month_already_exist = false;
                $first_of_this_month = Carbon\Carbon::today()->firstOfMonth()->toDateString();
                $first_of_next_month = Carbon\Carbon::today()->addMonth(1)->firstOfMonth()->toDateString();
            ?>
            <div class="portlet-body">
                <div>
                    Insentif sampai saat ini: {{HelperService::maskMoney($total_incentive)}}
                </div>
                <div class="table-scrollable">
                    <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th> Invoice Id </th>
                            <th> Insentif </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{$transaction->header->invoice_id}}
                            </td>
                            <td>{{HelperService::maskMoney($transaction->item_qty*$transaction->pic_incentive)}}</td>
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
