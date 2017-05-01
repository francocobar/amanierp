var FormValidation = function () {

    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            $.validator.addMethod("dateFormat",
                function(value, element) {
                    return value.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/);
                },
                "Tanggal tidak valid, pastikan dd-mm-yyyy.");

            var form1 = $('#form_give_new_password');

            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                messages: {
                    password: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    }
                },
                rules: {
                    password: {
                        minlength: 7,
                        maxlength: 20,
                        required: true
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
                    // success1.show();
                    // error1.hide();
                    validateForm(form1);

                }
            });


    }

    var handleValidation2 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form_new_salary');

            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    new_salary: {
                        required: true
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
                    // success1.show();
                    // error1.hide();
                    validateForm(form1);

                }
            });


    }

    return {
        //main function to initiate the module
        init: function () {
            handleValidation1();
            handleValidation2();
            // datepicker1();
        }

    };
}();

jQuery(document).ready(function() {
    FormValidation.init();
    MaskMoney.init();
    Bootbox.init();
});
