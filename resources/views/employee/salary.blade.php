<div class="row">
    <div class="col-md-6">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-history font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold">Riwayat Gaji</span>
                </div>
            </div>

            <?php
                $this_month_already_exist = $next_month_already_exist = false;
                $first_of_this_month = Carbon\Carbon::today()->firstOfMonth()->toDateString();
                $first_of_next_month = Carbon\Carbon::today()->addMonth(1)->firstOfMonth()->toDateString();
            ?>
            <div class="portlet-body" style="padding-left: 10px; padding-right: 10px;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th> Gaji </th>
                            <th> Berlaku Dari </th>
                            <th> Diset oleh </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee_salaries as $salary)
                        <?php
                            $can_be_deleted = $first_of_this_month==$salary->valid_since || $first_of_next_month==$salary->valid_since;
                            if($first_of_this_month==$salary->valid_since) {
                                $this_month_already_exist = true;
                            }

                            if($first_of_next_month==$salary->valid_since) {
                                $next_month_already_exist = true;
                            }

                            $valid_since = HelperService::inaDate($salary->valid_since);
                            $salary_string = HelperService::maskMoney($salary->employee_salary);
                        ?>
                        <tr>
                            <td>{{$salary_string}}
                                @if($can_be_deleted)
                                    <a data-btn-label="Hapus" data-confirm-type="0" data-message="Anda yakin ingin menghapus gaji {{$salary_string}} yang berlaku dari {{$valid_since}}?" class="bootbox-confirmationV2" href="{{route('delete.salary',['employee_salary_id'=>Crypt::encryptString($salary->id)])}}">
                                        [X]
                                    </a>
                                @endif
                            </td>
                            <td>{{$valid_since}}</td>
                            <td>{{$salary->user->first_name}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light portlet-fit portlet-form ">
            @if($this_month_already_exist == false || $next_month_already_exist == false)
            <div class="portlet-body">
                {!! Form::open(['id' => 'form_new_salary', 'route' => ['salary.employee.user', request()->employee_id, request()->user_id] ,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Gaji Baru
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="mask-money form-control" name="new_salary" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Gaji berlaku dari
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                @if($this_month_already_exist == false)
                                <label class="mt-radio">
                                    <input type="radio" name="salary_since" value="this_month" checked="">
                                     Bulan Ini
                                    <span></span>
                                </label>
                                @endif
                                @if($next_month_already_exist == false)
                                <label class="mt-radio">
                                    <input type="radio" name="salary_since" value="next_month" {{$this_month_already_exist ? 'checked=""' : ''}}>
                                     Bulan Depan
                                    <span></span>
                                </label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9 general-error">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn purple-rev">Simpan Gaji Baru</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
            @else
            <div class="portlet-body" style="padding:10px;">
                <h4 class="bold">Gaji bulan ini dan bulan depan sudah diset!</h4>
                Jika ingin menset ulang, silahkan hapus terlebih dahulu yang sudah diset.
            </div>
            @endif
        </div>
    </div>
</div>
