@extends('master')

@section('optional_js')
<script type="text/javascript">
jQuery(document).ready(function() {
    MaskMoney.init();

    $('#paid_value').change(function(){
        $('.general-error').html('');
        $('#debt_after').val('');
        var hutang = unmaskMoney($('#debt').val());
        var paid_value = unmaskMoney($('#paid_value').val());
        if(paid_value>hutang) {
            $('.general-error').html('Nilai yang mau dibayarkan tidak boleh melebih Nilai Hutang.');
            $(this).val('');
            return false;
        }
        $('#debt_after').val(maskMoney(hutang-paid_value));
        $('#total_paid2').trigger('change');
        return true;
    });

    $('#total_paid2').change(function(){
        $('.general-error').html('');
        if(isInputEmpty('#paid_value')) {
            $('.general-error').html('Isi Nilai yang mau dibayarkan!');
            $(this).val('');
            return false;
        }
        $('.submit-button-validation').prop('disabled',true);
        $('#change2').val('');

        var hutang = unmaskMoney($('#debt').val());
        var paid_value = unmaskMoney($('#paid_value').val());
        var total_bayar = unmaskMoney($(this).val());
        if(total_bayar<paid_value) {
            $('.general-error').html('Total Bayar harus lebih dari sama dengan Nilai yang mau di bayarkan.');
            $(this).val('');
            return false;
        }
        var kembalian = total_bayar-paid_value;
        $('#change2').val(maskMoney(kembalian));
        $('.submit-button-validation').prop('disabled', false);
        return true;
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
                    <span class="caption-subject font-purple-rev bold uppercase">Cicilan</span>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_pelunasan', 'route' => ['do.cashier.next-payment',request()->invoice],'class'=>'form-horizontal']) !!}
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
                        <?php $last_debt = $header->debt; ?>
                        @if($header->nextPayments)
                        <?php $count = 2; ?>
                        @foreach($header->nextPayments as $next_payment)
                        <div class="form-group">
                            <label class="control-label col-md-3">Pembayaran Ke-{{$count}}
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="{{$next_payment->paidValue(true)}}" disabled /> </div>
                            </div>
                        </div>
                        <?php
                            $last_debt = $next_payment->debt_after;
                            $count++;
                        ?>
                        @endforeach
                        @endif
                        <div class="form-group">
                            <label class="control-label col-md-3">Sisa Hutang
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="debt" type="text" class="form-control" value="{{HelperService::maskMoney($last_debt)}}" disabled /> </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="control-label col-md-3"> Pembayaran ke-{{$count}}
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="paid_value" type="text" class="mask-money required_val form-control" value="" name="paid_value"/>
                                </div>
                            </div>
                            <label class="control-label col-md-3"> (Nilai yang mau dibayarkan)
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Sisa Hutang
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="debt_after" type="text" class="mask-money form-control" value="" name="debt_after" disabled=""/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3"> Total Bayar
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
                            <label class="control-label col-md-3">Kembalian
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="change2" type="text" class="mask-money form-control" value="" name="change2" disabled=""/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Tipe Pembayaran
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <select id="payment_type" class="form-control" name="payment_type">
                                    <option value="1">Tunai</option>
                                    <option value="3">Credit Card</option>
                                    <option value="4">Debit Card</option>
                                </select>
                            </div>
                        </div>
                        @if(count($branches))
                        <div class="form-group">
                            <label class="control-label col-md-3">Cabang
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <select class="form-control required_val" name="branch">
                                    <option value="">Pilih Cabang</option>
                                    @foreach($branches as $branch)
                                        <option value="{{Crypt::encryptString($branch->id)}}">{{$branch->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
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
