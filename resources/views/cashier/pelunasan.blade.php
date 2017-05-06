@extends('master')

@section('optional_js')
<script src="{{ URL::asset('js/jquery.maskMoney.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    MaskMoney.init();

    $('#total_paid2').change(function(){
        $('.submit-button-validation').prop('disabled',true);
        $('#change2').val('');
        $('.general-error').html('');
        $('')
        var hutang = unmaskMoney($('#debt').val());
        var total_bayar = unmaskMoney($(this).val());
        var kembalian = maskMoney(total_bayar-hutang);
        if(kembalian<0) {
            $('.general-error').html('Pembayaran kurang!');
            return;
        }
        $('.submit-button-validation').prop('disabled', false);
        $('#change2').val(kembalian);

    });
});
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-wallet font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Pelunasan</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_pelunasan', 'route' => ['do.cashier.pleunasan',request()->invoice],'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Invoice Id
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$header->invoice_id}}" disabled /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Total Akhir Transaksi
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$header->totalTransaction(true)}}" disabled /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Pembayaran Pertama
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$header->firstPayment(true)}}" disabled /> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Jumlah Hutang
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="debt" type="text" class="form-control" value="{{HelperService::maskMoney($header->debt)}}" disabled /> </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="control-label col-md-3">Pembayaran Kedua (Pelunasan)
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="total_paid2" type="text" class="mask-money required_val form-control" value="" name="total_paid2"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Kembali
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="change2" type="text" class="mask-money form-control" value="" name="change2" disabled=""/>
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
                                <div class="mt-checkbox-list" data-error-container="#form_2_services_error">
                                    <label class="mt-checkbox">
                                        <input type="checkbox" value="1" name="print" checked> Print Struk
                                        <span></span>
                                    </label>
                                </div>
                                <button data-unique ='.required_val' type="submit" class="submit-button-validation btn purple-rev" disabled>Simpan</button>
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
