var FormValidation = function () {

    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form_add_voucher');
            // var error1 = $('.alert-danger', form1);
            // var success1 = $('.alert-success', form1);

            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                messages: {
                    voucher_name: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    },
                    number_of_vouchers: {
                        number: "Hanya boleh angka",
                        min: jQuery.validator.format("Minimal {0}."),
                    },
                    discount_value: {
                        number: "Hanya boleh angka",
                        min: jQuery.validator.format("Minimal {0}."),
                        max: jQuery.validator.format("Maksimal {0} persen."),
                    },
                },
                rules: {
                    voucher_name: {
                        minlength: 3,
                        maxlength: 25,
                        required: true
                    },
                    number_of_vouchers: {
                        number: true,
                        required: true,
                        min:1
                    },
                    discount_value: {
                        number: true,
                        required: true,
                        min:1,
                        max:function(element) {
                            if ($("input[name='discount_type']:checked").val() == '1')
                                return 100;
                        }
                    },
                    // required: "This field is requiredsss.",
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    // success1.hide();
                    // error1.show();
                    // App.scrollTo(error1, -200);
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
                    // success1.show();
                    // error1.hide();
                    form1.find('.submitButton').attr('data-ready', true).trigger('click');
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
    FormValidation.init();
});
