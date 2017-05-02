<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            @if(strtolower($role_user_login->slug) == 'manager' || request()->route()->getName()=='dashboard')
            <div class="portlet-title">
                <div class="caption">
                    @if(request()->route()->getName()=='dashboard' || request()->user_id == Sentinel::getUser()->id)
                    <i class="fa fa-gear font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold">Pengaturan Akun</span>
                    @else
                    <i class="fa fa-users font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold">Karyawan:
                        {{ $employee_data->full_name }}
                        ({{request()->employee_id}})</span>
                    @endif
                </div>
            </div>
            @endif
            <div class="portlet-body">
                {!! Form::open(['id' => 'form_give_new_password', 'route' => ['password.employee.user', request()->employee_id, request()->user_id] ,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Password Baru
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="password" class="form-control" name="password" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn purple-rev">Ganti Password</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
