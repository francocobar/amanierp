@extends('master')

@section('optional_css')
<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
@endsection

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../js/stock.js" type="text/javascript"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-table font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Input Stok Pusat</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_stock', 'route' => ['input.stock.item.do', request()->item_id],'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Produk
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$item->item_id.' '.$item->item_name}} " disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Stok di pusat saat ini
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$stock}}" disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Modal per pcs saat ini
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{HelperService::maskMoney($modal_per_pcs)}}" disabled/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Tambahkan stok sebanyak
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="" name="add_stock">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Modal per pcs
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="mask-money form-control" value="" name="modal_per_pcs">
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
