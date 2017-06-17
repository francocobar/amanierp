@extends('master')

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../js/add-voucher.js" type="text/javascript"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-vimeo font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Tambah Voucher</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_add_voucher', 'route' => 'add.voucher.do','class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group margin-top-20">
                            <label class="control-label col-md-3">Nama Voucher
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" placeholder="" class="form-control" name="voucher_name" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Jumlah Voucher
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" placeholder="" class="form-control" name="number_of_vouchers" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nilai Diskon
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" placeholder="" class="form-control" name="discount_value" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tipe Diskon
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <label class="mt-radio">
                                    <input type="radio" name="discount_type" id="discount_type1" value="1" checked="">
                                     Persen
                                    <span></span>
                                </label>
                                <label class="mt-radio">
                                    <input type="radio" name="discount_type" id="discount_type2" value="2">
                                     Nilai Pasti
                                    <span></span>
                                </label>
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
                                <button type="submit" class="btn purple-rev">Tambah Voucher</button>
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
