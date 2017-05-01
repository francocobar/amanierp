var FormValidation = function () {

    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            var form1 = $('#form_stock');

            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                messages: {
                    add_stock: {
                        min: "Minimal 1",
                        number: "Hanya boleh angka!"
                    },
                    branch: {
                        required: "Harap pilih cabang karyawan yang ingin disupply produk ini."
                    }
                },
                rules: {
                    add_stock: {
                        number: true,
                        min: 1,
                        required: true
                    },
                    branch: {
                        required: true
                    }
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
                    if(form1.find('button[type="submit"]').attr('data-trigger-click')) {
                        $(form1.find('button[type="submit"]').attr('data-trigger-click')).trigger('click');
                    }
                    else {
                        validateForm(form1);
                    }
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

    $('.supply_branch').click(function(e){
        e.preventDefault();
        location.href = $(this).attr('href') + $("select[name='branch']").val();
    });
});
