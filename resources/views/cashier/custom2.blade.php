@extends('master')

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
var FormValidation = function () {
    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            var form1 = $('#form_custom_2');

            // if($.trim(form1.find('.mask-money').val()) == '0') {
            //     form1.find('.mask-money').val('');
            // }
            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                messages: {
                    customer_name: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    },
                    phone: {
                        number: "Hanya boleh angka, disarankan awali dengan 0 atau 62",
                        minlength: jQuery.validator.format("Minimal 7 digit."),
                        maxlength: jQuery.validator.format("Maksimal 20 digit.")
                    },
                },
                rules: {
                    customer_name: {
                        minlength: 3,
                        maxlength: 100,
                        required: true
                    },
                    phone: {
                        number: true,
                        minlength: 7,
                        maxlength: 20,
                        required: true
                    },
                    payment_type: {
                        required: true
                    },
                    paid_value: {
                        required: true
                    },
                    total_paid: {
                        required: true
                    },
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                    $(element)
                        .closest('form').find('.submitButton').attr('data-ready', false);
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.form-group').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    validateForm(form1);

                }
            });


    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation1();
        }

    };
}();

jQuery(document).ready(function() {
    MaskMoney.init();
    FormValidation.init();

    $('#others').change(function(){
        var others =  $.trim($('#others').val()) == '' ? 0 : unmaskMoney($('#others').val());
        var grand_total = unmaskMoney($('#grand_total').val());
        $('#grand_total_2').val(maskMoney(grand_total+others));
    });

    $('#lunas').click(function(){
        $('#payment_type').trigger('change');
    });

    $('#paid_value').change(function(){
        $('#paid_value_temp').val($(this).val());
        $('#total_paid').trigger('change');
    });

    $('#payment_type').change(function(){
        if($('#payment_type').val()!='') {
            if($('input[name="lunas"]').is(':checked')) {
                var others =  $.trim($('#others').val()) == '' ? 0 : unmaskMoney($('#others').val());
                var grand_total = unmaskMoney($('#grand_total').val());
                $('#paid_value').val(maskMoney(grand_total+others));
                $('#paid_value_temp').val(maskMoney(grand_total+others));
                $('#paid_value').prop('disabled', true);
            }
            else {
                $('#paid_value').prop('disabled', false);
                $('#paid_value').val('');
                $('#paid_value_temp').val('');
            }
        }
        $('#total_paid').trigger('change');
    });

    $('#total_paid').change(function(){
        if($.trim($(this).val()) != '') {
            var kembalian = unmaskMoney($('#total_paid').val()) - unmaskMoney($('#paid_value').val());
            if(kembalian<0) {
                alert('Pembayaran tidak cukup! Minimal '+$('#paid_value').val());
                $('#total_paid').val($('#paid_value').val());
                $('#total_paid').trigger('change');
            }
            else {
                $('#change').val(maskMoney(kembalian));
            }
        }
        else {
            $('#change').val(0);
        }
    });

});
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-wallet font-purple-rev"></i>
                    @if(UserService::isSuperAdmin() || $employee_data == null)
                    <span class="caption-subject font-purple-rev bold uppercase">Custom Cashier</span>
                    @else
                    <span class="caption-subject font-purple-rev bold uppercase">Custom Cashier | Cabang: {{$employee_data->branch->branch_name}} </span>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                {!! Form::open(['id' => 'form_custom_2', 'route' => ['do.custom.cashier'],'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        @if(UserService::isSuperAdmin() || $employee_data == null)
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <select id="cashier_branch_temp" class="form-control" name="cashier_branch_temp">
                                        <option value="">* Pilih Cabang Transaksi Ini</option>
                                            @foreach(App\Branch::all() as $branch)
                                            <option value="{{Crypt::encryptString($branch->id)}}">{{$branch->branch_name}}</option>
                                            @endforeach
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="control-label col-md-3">Grand Total
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="grand_total" type="text" class="form-control" disabled="" value="{{HelperService::maskMoney($grand_total)}}"/> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Biaya Lain-lain
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="others" type="text" class="mask-money form-control" name="others"/> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Grand Total Akhir
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="grand_total_2" type="text" class="form-control" disabled="" value="{{HelperService::maskMoney($grand_total)}}"/> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-4">
                                <label class="mt-checkbox">
                                    <input id="lunas" type="checkbox" value="1" name="lunas" checked> Lunas
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <select id="payment_type" class="form-control" name="payment_type">
                                        <option value="">* Pilih Tipe Pembayaran</option>
                                            <option value="1">Tunai</option>
                                            <option value="3">Credit Card</option>
                                            <option value="4">Debit Card</option>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="hidden" id="paid_value_temp" name="paid_value_temp" />
                                    <input type="text" class="mask-money form-control" placeholder="* Nilai yang ingin di bayarkan" id="paid_value" name="paid_value"/> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="mask-money form-control" placeholder="* Jumlah Bayar" id="total_paid" name="total_paid"/> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Kembalian
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="change" type="text" class="form-control" disabled="" value="0"/> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Customer
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" name="customer_name" /> </div>
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

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9 general-error">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn purple-rev">Selesaikan Transaksi</button>
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
