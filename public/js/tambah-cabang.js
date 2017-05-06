var FormValidation = function () {

    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form_add_cabang');
            // var error1 = $('.alert-danger', form1);
            // var success1 = $('.alert-success', form1);

            jQuery.validator.addMethod("lettersonly", function(value, element) {
                return this.optional(element) || /^[a-z]+$/i.test(value);
            }, "Hanya boleh huruf.");
            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                messages: {
                    branch_name: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    },
                    address: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    },
                    phone: {
                        number: "Hanya boleh angka, disarankan awali dengan 0 atau 62",
                        minlength: jQuery.validator.format("Minimal 7 digit."),
                        maxlength: jQuery.validator.format("Maksimal 20 digit.")
                    },
                    prefix: {
                        minlength: jQuery.validator.format("Harus {0} karakter."),
                        maxlength: jQuery.validator.format("Harus {0} karakter.")
                    }
                },
                rules: {
                    branch_name: {
                        minlength: 3,
                        maxlength: 50,
                        required: true
                    },
                    address: {
                        minlength: 10,
                        maxlength: 100,
                        required: true
                    },
                    phone: {
                        number: true,
                        minlength: 7,
                        maxlength: 20,
                        required: true
                    },
                    prefix: {
                        required:true,
                        lettersonly: true,
                        minlength: 3,
                        maxlength: 3
                    }
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
