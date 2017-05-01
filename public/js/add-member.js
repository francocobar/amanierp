var FormValidation = function () {

    var datepicker1 = function() {
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            yearRange: "1900:2046"
        });
    };

    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            $.validator.addMethod("dateFormat",
                function(value, element) {
                    return value.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/);
                },
                "Tanggal tidak valid, pastikan dd-mm-yyyy.");

            var form1 = $('#form_add_member');

            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                messages: {
                    member_id: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    },
                    full_name: {
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
                    },
                    email: {
                        email: "Masukkan email yang valid."
                    },
                    branch: {
                        required: "Harap pilih cabang utama member / tempat didaftarkan."
                    }
                },
                rules: {
                    member_id: {
                        minlength: 3,
                        maxlength: 8
                    },
                    full_name: {
                        minlength: 3,
                        maxlength: 100,
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
                    email: {
                        email:true,
                    },
                    branch: {
                        required: true,
                    },
                    dob: {
                        required: true,
                        dateFormat: true,
                    },
                    member_since: {
                        required: true,
                        dateFormat: true,
                    }
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
                    validateForm(form1);
                }
            });


    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation1();
            datepicker1();
        }

    };
}();

jQuery(document).ready(function() {
    FormValidation.init();
});
