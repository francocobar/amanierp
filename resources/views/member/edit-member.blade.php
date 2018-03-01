@extends('master')

@section('optional_css')
<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
@endsection

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="../js/add-member.js" type="text/javascript"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-user-plus font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Tambah Member</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_edit_member', 'route' => 'add.member.do', 'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Id Member
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" placeholder="KOSONGKAN kecuali menggunakan Id lama"class="form-control" name="member_id" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Member
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" name="full_name" /> </div>
                            </div>
                        </div>
                        <div class="form-group  margin-top-20">
                            <label class="control-label col-md-3">Tempat Lahir
                                <span class="required">  </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" name="place_of_birth" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tanggal Lahir
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-group input-medium">
                                    <input type="text" placeholder="dd-mm-yyyy" class="datepicker form-control" id="dob" name="dob">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nomor Hp
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" name="phone" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Email
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" name="email" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Alamat
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <textarea class="form-control" rows="3" name="address"></textarea></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tinggal di
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" placeholder="Kota/Kabupaten/Provinsi"class="form-control" name="stay_at" /> </div>
                            </div>
                        </div>
                        @if($role_slug == 'superadmin')
                        <div class="form-group">
                            <label class="control-label col-md-3">Cabang
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>

                                    <select class="form-control" name="branch">
                                        <option value="">Pilih Cabang</option>
                                        @foreach($branches as $branch)
                                            <option value="{{Crypt::encryptString($branch->id)}}">{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @elseif($role_slug == 'manager')
                            <input type="hidden" class="form-control" value="add_by_manager" name="branch" />
                        @endif
                        <div class="form-group">
                            <label class="control-label col-md-3">Member Sejak
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-group input-medium">
                                    <input type="text" placeholder="dd-mm-yyyy" class="datepicker form-control" name="member_since">
                                </div>
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
                                <button type="submit" class="btn purple-rev">Submit</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
            </div>
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
@endsection
