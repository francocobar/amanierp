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
                    <span class="caption-subject font-purple-rev bold uppercase">Edit Item</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_add_item', 'route' => 'update.item','class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Item
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" name="item_name" value="{{$item->item_name}}" /> </div>
                                    <input type="hidden" name="item" value="{{Crypt::encryptString($item->item_id)}}" />
                            </div>
                        </div>
                        @if($item->item_type == Constant::type_id_produk || $item->item_type == Constant::type_id_sewa)
                        <div class="form-group">
                            <label class="control-label col-md-3">Harga Cabang
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="{{$item->branch_price >0 ? HelperService::maskMoney($item->branch_price) : ''}}" class="form-control mask-money" name="branch_price" /> </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="control-label col-md-3">Harga Member
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="{{HelperService::maskMoney($item->m_price)}}" class="form-control mask-money" name="m_price" /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Harga Umum
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="{{HelperService::maskMoney($item->nm_price)}}" class="form-control mask-money" name="nm_price" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tipe Item
                                <span class="required"> </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" name="item_type" value="{{HelperService::itemTypeById($item->item_type)}}" class="form-control" disabled/> </div>
                            </div>
                        </div>
                        @if($item->item_type == Constant::type_id_jasa || $item->item_type == Constant::type_id_paket)
                        <div class="form-group" id="input_incentive">
                            <label class="control-label col-md-3">Insentif
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" value="{{HelperService::maskMoney(ItemService::getLatestIncentive($item->item_id,'!obj'))}}" class="form-control mask-money" name="incentive" /> </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="control-label col-md-3">Deskripsi
                                <span class="required"> </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <textarea class="form-control" rows="3" name="description">{{$item->description}}</textarea></div>
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
