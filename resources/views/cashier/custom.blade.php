@extends('master')

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
var FormValidation = function () {
    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            var form1 = $('#form_custom');

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
                    custom_name: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    }
                },
                rules: {
                    custom_name: {
                        minlength: 3,
                        maxlength: 100,
                        required: true
                    },
                    item_price: {
                        required: true,
                    },
                    item_qty: {
                        required: true,
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

    $('#item_price').change(function(){
        var item_total_price = item_price = item_qty = fixed_discount = 0;
        if(!isInputEmpty('#item_price')) {
            item_price = unmaskMoney($('#item_price').val());
        }
        if(!isInputEmpty('#item_qty')) {
            item_qty = parseInt($('#item_qty').val());
        }
        if(!isInputEmpty('#item_discount_fixed_value')) {
            fixed_discount = unmaskMoney($('#item_discount_fixed_value').val());
        }
        item_total_price = (item_price * item_qty) - fixed_discount;

        $('#item_total_price').val(maskMoney(item_total_price));
    });

    $('#item_qty').change(function(){
        $('#item_price').trigger('change');
    });

    $('#item_discount_fixed_value').change(function(){
        $('#item_price').trigger('change');
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
                {!! Form::open(['id' => 'form_custom', 'route' => ['custom.cashier.add.detail'],'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Item
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input id="custom_name" type="text" class="form-control" value="" name="custom_name" /> </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Harga Item / Qty
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="mask-money form-control" value="" id="item_price" name="item_price" /> </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Qty
                                <span class="required">*</span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="" id="item_qty" name="item_qty" /> </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Diskon (nilai pasti)
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="mask-money form-control" value="" id="item_discount_fixed_value" name="item_discount_fixed_value" /> </div>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="control-label col-md-3">Total Harga Item
                                <span class="required"></span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <input type="text" class="form-control" value="" id="item_total_price" disabled=""/> </div>
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
                                <button type="submit" class="btn purple-rev">Tambahkan</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
            </div>

            @if(count($custom_details)>0)
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list-ol font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Added Items <a href="{{route('get.custom.cashier',['remove'=>'all'])}}">[RESET]</a></span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="text-right">
                    <a class="btn btn-success" href="{{route('get.custom.cashier.finishing')}}">Selanjutnya</a>
                </div>
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width:30px !important">&nbsp;</th>
                                <th scope="col" style="width:30px !important" class="text-right">No.</th>
                                <th scope="col">Nama Item</th>
                                <th scope="col">Harga / Qty</th>
                                <th scope="col" style="width:30px !important">Qty</th>
                                <th scope="col">Sub Total</th>
                                <th scope="col">Potongan</th>
                                <th scope="col">Sub Total Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $grand_sub_total = 0;
                                $grand_potongan = 0;
                            ?>
                            @foreach($custom_details as $custom_detail)
                            <tr>
                                <td class="text-center"><a href="{{route('get.custom.cashier',['remove'=>$no])}}">[x]</a></td>
                                <td class="text-right">{{$no}}</td>
                                <td>{{$custom_detail['custom_name']}}</td>
                                <td>{{HelperService::maskMoney($custom_detail['item_price'])}}</td>
                                <td>{{$custom_detail['item_qty']}}</td>
                                <?php
                                    $sub_total = intval($custom_detail['item_price']) *  $custom_detail['item_qty'];
                                    $potongan = $custom_detail['item_discount_fixed_value'];
                                    $grand_sub_total += $sub_total;
                                    $grand_potongan += $potongan;
                                    $no++;
                                ?>
                                <td>{{HelperService::maskMoney($sub_total)}}</td>
                                <td>{{HelperService::maskMoney($potongan)}}</td>
                                <td>{{HelperService::maskMoney($sub_total-$potongan)}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="5" class="bold text-right">
                                    Grand Total
                                </td>
                                <td class="bold">{{HelperService::maskMoney($grand_sub_total)}}</td>
                                <td class="bold">{{HelperService::maskMoney($grand_potongan)}}</td>
                                <td colspan="2" class="bold">{{HelperService::maskMoney($grand_sub_total-$grand_potongan)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
@endsection
