@extends('master')

@section('optional_css')
<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
@endsection

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../js/add-item.js" type="text/javascript"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-database font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Tambah Item</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_add_item', 'route' => 'add.item.do','class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Item
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" name="item_name" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tipe Item
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <select class="form-control" name="item_type">
                                        <option value="">Pilih Tipe Item</option>
                                        @foreach($item_types as $type => $value)
                                            <option value="{{$type}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="input_branch_price" style="display: none;">
                            <label class="control-label col-md-3">Harga Cabang
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="" class="form-control mask-money" name="branch_price" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Harga Member
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="" class="form-control mask-money" name="m_price" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Harga Umum
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="" class="form-control mask-money" name="nm_price" /> </div>
                            </div>
                        </div>
                        <div class="form-group" id="input_incentive" style="display: none;">
                            <label class="control-label col-md-3">Insentif
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="" class="form-control mask-money" name="incentive" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Deskripsi
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <textarea class="form-control" rows="3" name="description"></textarea></div>
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
                                <button type="button" class="btn default">Cancel</button>
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
