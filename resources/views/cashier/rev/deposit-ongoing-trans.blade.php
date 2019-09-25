@extends('master.master-pos')

@section('content')
    <div class="container marketing">
        <div class="row">
            <div class="col-lg-12 right">
                <a href="{{route('cashier.index', request()->branch_id)}}"><button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali ke Beranda Kasir</button></a>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div style="margin: 60px">
                    <h3>Jumlah deposit yang diperlukan <u id="jsCustomerData"></u></h3>
                    <div>
                        <span id="jsFetchLoading" class="blink-me" style="margin: 0 auto;">fetching data . . .</span>
                        <span id="jsGrandTotal"></span>
                    </div>
                </div>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-6">
                <h2>Deposit secara</h2>
                <button disabled id="jsBtnFullPayment" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block" data-toggle="modal" data-target="#jsModalFullPayment">Penuh</button>
                <button disabled id="jsBtnDownPayment" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block" data-toggle="modal" data-target="#jsModalDownPayment">Sebagian</button>

            </div>
        </div>
</div>
@endsection

@section('blade-hidden')
<input type="hidden" id="jsUrlGetOngoingTransData" value="{{route('cashier.get-ongoing-transaction', request()->trans_id)}}" />
<div class="modal fade" id="jsModalFullPayment" role="dialog" aria-labelledby="jsModalFullPayment" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsModalTitleFullPayment">Bayar Penuh: <span class="font-weight-bold"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        Metode Pembayaran:
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                          <label class="btn btn-secondary active">
                              <input value="1" type="radio" name="payment_method" id="option1" autocomplete="off" checked> Uang Cash
                          </label>
                          <label class="btn btn-secondary">
                              <input value="2" type="radio" name="payment_method" id="option2" autocomplete="off"> Kartu Debit
                          </label>
                          <label class="btn btn-secondary">
                              <input value="3" type="radio" name="payment_method" id="option3" autocomplete="off"> Kartu Kredit
                          </label>
                        </div>
                    </div>
                </div>
                <div class="row js-card-number" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsCardNumber" type="text" class="form-control" placeholder="Nomor pada Kartu"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsAmountToCashier" type="text" class="form-control js-mask-idr js-default-empty" placeholder="* Uang yang diserahkan ke Kasir"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        Kembalian: <b>Rp<span id="jsPaymentChange">0</span></b>
                    </div>
                </div>
                <div id="jsFinishPayment" class="row js-card-number" style="margin-top: 6px; display: hidden;">
                    <div class="col-md-12 text-right">
                        <button id="jsBtnFinishPayment" type="button" class="btn btn-primary">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="jsModalDownPayment" role="dialog" aria-labelledby="jsModalDownPayment" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsModalTitleDownPayment">Bayar Uang Muka Ke-1</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input id="jsAmountDP" type="text" class="form-control js-mask-idr js-default-empty" placeholder="* Uang muka yang ingin dibayarkan"/>
                    </div>
                </div>
                <div class="row" style="margin-top: 6px">
                    <div class="col-md-12">
                        Metode Pembayaran:
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                          <label class="btn btn-secondary active">
                              <input value="1" type="radio" name="payment_method" id="option4" autocomplete="off" checked> Uang Cash
                          </label>
                          <label class="btn btn-secondary">
                              <input value="2" type="radio" name="payment_method" id="option5" autocomplete="off"> Kartu Debit
                          </label>
                          <label class="btn btn-secondary">
                              <input value="3" type="radio" name="payment_method" id="option6" autocomplete="off"> Kartu Kredit
                          </label>
                        </div>
                    </div>
                </div>
                <div class="row js-card-number" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsCardNumber2" type="text" class="form-control" placeholder="Nomor pada Kartu"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsAmountToCashier2" type="text" class="form-control js-mask-idr js-default-empty" placeholder="* Uang yang diserahkan ke Kasir"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        Kembalian: <b>Rp<span id="jsPaymentChange2">0</span></b>
                    </div>
                </div>
                <div id="jsFinishPayment2" class="row js-card-number" style="margin-top: 6px; display: hidden;">
                    <div class="col-md-12 text-right">
                        <button id="jsBtnFinishPayment2" type="button" class="btn btn-primary">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Form::open(['id' => 'jsFormPayOngoingTransaction', 'route' => array('cashier.pays-ongoing-transaction',request()->branch_id,request()->trans_id)]) !!}
<input id="jsPaymentMethod" type="hidden" name="payment_method" value="" />
<input id="jsValAmountToPay" type="hidden" name="amount_to_pay" value="" />
<input id="jsValAmountToCashier" type="hidden" name="amount_to_cashier" value="" />
<input id="jsValCardNumber" type="hidden" name="card_number" value="" />
{!! Form::close() !!}
@endsection

@section('blade-script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script type="text/javascript">
    var grandTotal = 0;
    $(document).ready(function(){
        $('#jsModalFullPayment').on('show.bs.modal', function (e) {
            $('#option1').trigger('click');
            $('#option1').trigger('change');
        });

        $('#jsModalDownPayment').on('show.bs.modal', function (e) {
            $('#jsAmountDP').val('');
            $('#jsAmountToCashier2').val('');
            $('#jsCardNumber2').val('');
            $('#option4').trigger('click');
            $('#option4').trigger('change');
        });

        $('#jsBtnFullPayment').click(function(e){
            e.preventDefault();
            console.log('ok');
        });

        $('#jsBtnDownPayment').click(function(e){
            e.preventDefault();
            console.log('ok2');
        });

        $('#jsAmountDP').change(function(e) {
            calculateChange2();
        });

        $("input[name='payment_method']").change(function(e){
            $('.js-cash').hide();
            $('#jsPaymentChange').html('0');
            $('#jsCardNumber').val('');
            $('#jsAmountToCashier').val('');
            $('.js-card-number').hide();
            if($(this).val() == '1') {
                console.log('payment method uang cash');
                $('.js-cash').show();
                if($(this).attr('id') == 'option4') {
                    $('#jsCardNumber2').val('');
                    calculateChange2();
                }
            }
            else if($(this).val() == '2' || $(this).val() == '3') {
                console.log('payment method kartu debit / kredit');
                $('.js-card-number').show();
            }
            else {
                location.reload();
            }
        });

        $('#jsAmountToCashier').change(function(e){
            var amount_to_cashier = $('#jsAmountToCashier').maskMoney('unmasked')[0] * 1000;
                $('#jsPaymentChange').html(maskMoney(amount_to_cashier-grandTotal));
            if(amount_to_cashier>=grandTotal) {
                $('#jsFinishPayment').show();
            } else {
                // $('#jsPaymentChange').html('0');
                // $('#jsAmountToCashier').val('');
                $('#jsFinishPayment').hide();
                toastr["error"]("Uang yang diserahkan tidak cukup", "");
            }
        });

        $('#jsAmountToCashier2').change(function(e){
            var amount_to_cashier = $('#jsAmountToCashier2').maskMoney('unmasked')[0] * 1000;
            calculateChange2();
        });

        function calculateChange2()
        {
            var amount_dp = $('#jsAmountDP').maskMoney('unmasked')[0] * 1000;
            if(amount_dp >= grandTotal) {
                toastr["warning"]("Uang muka harus lebih kecil dari jumlah tagihan", "");
                // $('#jsAmountDP').val('');
            }
            else if($('input[name="payment_method"]:checked').val() == '1') {

                $('#jsFinishPayment2').hide();
                var amount_to_cashier = $('#jsAmountToCashier2').maskMoney('unmasked')[0] * 1000;
                if(amount_to_cashier == 0) {
                    $('#jsPaymentChange2').html('0');
                }
                else if(amount_dp != 0) {
                    $('#jsPaymentChange2').html(maskMoney(amount_to_cashier-amount_dp));
                    if(amount_to_cashier>=amount_dp) {
                        $('#jsFinishPayment2').show();
                    } else {
                        // $('#jsPaymentChange2').html('0');
                        // $('#jsAmountToCashier2').val('');
                        toastr["error"]("Uang yang diserahkan tidak cukup", "");
                    }
                }
            }
            // else {
            //     var amount_dp = $('#jsAmountDP').maskMoney('unmasked')[0] * 1000;
            //     if(amount_dp>0) {
            //         $('#jsFinishPayment2').show();
            //     }
            //     else {
            //
            //     }
            //
            // }
        }

        $('#jsBtnFinishPayment').click(function(e){
            submitPayment(true);
        });

        $('#jsBtnFinishPayment2').click(function(e){
            submitPayment(false);
        });

        function submitPayment(is_full_payment)
        {
            var payment_method = $('input[name="payment_method"]:checked').val();
            $('#jsPaymentMethod').val(payment_method);
            if(is_full_payment) {
                $('#jsValAmountToPay').val(grandTotal);
                if(payment_method != '1') {
                    $('#jsValCardNumber').val($('#jsCardNumber').val());
                    $('#jsValAmountToCashier').val(grandTotal);
                }
                else {
                    $('#jsValCardNumber').val('');
                    $('#jsValAmountToCashier').val($('#jsAmountToCashier').maskMoney('unmasked')[0] * 1000);
                }

            }
            else {
                $('#jsValAmountToPay').val($('#jsAmountDP').maskMoney('unmasked')[0] * 1000);
                if(payment_method != '1') {
                    $('#jsValCardNumber').val($('#jsCardNumber2').val());
                    $('#jsValAmountToCashier').val($('#jsAmountDP').maskMoney('unmasked')[0] * 1000);
                }
                else {
                    $('#jsValCardNumber').val('');
                    $('#jsValAmountToCashier').val($('#jsAmountToCashier2').maskMoney('unmasked')[0] * 1000);
                }
            }

            $form = $('#jsFormPayOngoingTransaction');
            $.ajax({
        		url: $form.attr('action'),
        		method:"POST",
        		dataType:'JSON',
          	    async: false,
        		data:$form.serializeArray(),
		        success:function(response){
                },
                error: function (request, status, error) {
                    // showErrorMessageByResponseCode(request.status);
                },
                beforeSend: function() {
                    hideAllModals();
                    loadingWithMessage('Sedang mencatat pembayaran . . .');
                },
                complete: function() {
                    hideAllModals();
                }
            });
        }//end submitPayment()

        $.ajax({
    		url: $('#jsUrlGetOngoingTransData').val(),
    		method:"POST",
    		dataType:'JSON',
      	    async: false,
    		success:function(response){
                if(response.data) {
                    $('#jsUrlGetOngoingTransData').remove();
                    $.each(response.data.detail_data, function( key, value ) {
                        grandTotal = parseFloat(grandTotal) + parseFloat(value.item_price * value.item_qty)
                    });
                    var discount_fix_value = 0;
                    if(response.data.coupon_data) {
                        var coupon = response.data.coupon_data;
                        if(coupon.disc_value_type == 2) {
                            if(grandTotal > coupon.disc_value) {
                                discount_fix_value = grandTotal;
                            }
                            else {
                                discount_fix_value = coupon.disc_value;
                            }
                        }
                        else {
                            discount_fix_value = parseInt(coupon.disc_value) / 100 * grandTotal;
                            if(coupon.max_fix_value && discount_fix_value>coupon.max_fix_value) {
                                discount_fix_value = coupon.max_fix_value;
                            }
                        }
                    }
                    $('#jsBtnFullPayment').prop('disabled', false);
                    $('#jsBtnDownPayment').prop('disabled', false);
                    $('#jsFetchLoading').remove();
                    grandTotal = grandTotal-discount_fix_value;
                    $('#jsGrandTotal').html('Rp'+maskMoney(grandTotal));
                    $('#jsModalTitleFullPayment').find('span').html('Rp'+maskMoney(grandTotal));
                    $('#jsCustomerData').html(response.data.header_data.member_id + ' - ' + response.data.header_data.customer_name + ' #' + response.data.header_data.trans_id);

                }
            },
            error: function (request, status, error) {
                showErrorMessageByResponseCode(request.status);
            },
        });
    });
</script>
@endsection

@section('blade-style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
@endsection
