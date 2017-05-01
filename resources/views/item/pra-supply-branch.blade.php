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
                    <span class="caption-subject font-purple-rev bold uppercase">Supply ke Cabang</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_stock','class'=>'form-horizontal']) !!}
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
                                    <input type="text" class="form-control" value="{{$stock_pusat}}" disabled/>
                                </div>
                            </div>
                        </div>
                        @if($stock_pusat>0)
                        <div class="form-group">
                            <label class="control-label col-md-3">Pilih Cabang
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
                        @endif
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9 general-error">
                                @if($stock_pusat<=0)
                                    Anda tidak dapat melakukan supply ke Cabang karena stok item ini sedang kosong di Pusat.<br/>
                                    <a href="{{route('input.stock.item',['item_id'=>request()->item_id])}}">Tambah Stok Pusat</a>
                                @endif
                            </div>
                        </div>
                        @if($stock_pusat>0)
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button data-trigger-click=".supply_branch" type="submit" class="btn purple-rev">Submit</button>
                                <a href="{{route('supply.stock.item',['item_id' => request()->item_id, 'branch_id' => ''])}}" class="supply_branch">
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
            </div>
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
@endsection
