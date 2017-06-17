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
                    <span class="caption-subject font-purple-rev bold uppercase">Supply ke Cabang: {{$branch->branch_name}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_stock', 'route' => ['supply.stock.item.do', request()->item_id, request()->branch_id],'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-5">Produk
                                <span class="required"></span>
                            </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$item->item_id.' '.$item->item_name}} " disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5">Stok di pusat saat ini
                                <span class="required"></span>
                            </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{intval($stock_pusat)}}" disabled/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-5">Stok di Cabang {{$branch->branch_name}}  saat ini
                                <span class="required"></span>
                            </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$branch_stock}}" disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5">Harga Jual ke Cabang (per pcs)
                                <span class="required"></span>
                            </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" id="branch_price" class="form-control" value="{{HelperService::maskMoney($item->branch_price)}}" disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5">Tambahkan stok di Cabang {{$branch->branch_name}} sebanyak
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="input_add_stock" type="text" class="form-control" value="" name="add_stock">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-5">
                                Jumlah yang dibayarkan cabang:
                            </label>
                            <label id="preview-harga" class="bold control-label col-md-5" style="text-align: left !important;">
                                0
                            </label>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-5">Note
                                <span class="required"></span>
                            </label>
                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <textarea class="form-control" rows="3" name="note"></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-5 col-md-7 general-error">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-5 col-md-7">
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
