<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-child font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold">Karyawan:
                        {{ $employee_data->full_name }}
                        ({{request()->employee_id}})</span>
                </div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['id' => 'form_change_role', 'route' => ['role.employee.user', request()->employee_id, request()->user_id] ,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Role
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <select class="form-control" name="role">
                                        @foreach(Sentinel::getRoleRepository()->get() as $role)
                                            <option {{$role->id == $role_user->id ? 'selected' : ''}} value="{{Crypt::encryptString($role->id)}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn purple-rev submit-button">Ganti Role</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
