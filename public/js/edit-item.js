var FormValidation = function () {

    var onChangeItemType = function() {
        $("select[name='item_type']").change(function(){
            if($.trim($(this).find("option:selected").text()) == 'Jasa') {
                console.log("oke");
                $('#input_incentive').show();
            }
            else {
                console.log("okes");
                $('#input_incentive').hide();
            }
        });
    }

    var maskMoney = function() {
        $(".mask-money").maskMoney({
            thousands:'.',
            allowZero: false,
            precision:0
        });
    }
    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            var form1 = $('#form_add_item');

            form1.submit(function(e) {
                e.preventDefault();
            }).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                messages: {
                    item_name: {
                        maxlength: jQuery.validator.format("Maksimal {0} karakter."),
                        minlength: jQuery.validator.format("Minimal {0} karakter.")
                    },
                    item_types: {
                        required: "Harap pilih tipe tipe item."
                    }
                },
                rules: {
                    item_name: {
                        minlength: 3,
                        maxlength: 100,
                        required: true
                    },
                    m_price: {
                        required: true
                    },
                    nm_price: {
                        required: true
                    },
                    item_type: {
                        required: true,
                    },
                    incentive: {
                        required: function(elemment) {
                            // console.log($.trim($('select[name="item_type"').find("option:selected").text()));
                            return $.trim($('select[name="item_type"').find("option:selected").text()) == "Jasa";
                        }
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
                    validateForm(form1);

                }
            });


    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation1();
            maskMoney();
            onChangeItemType();
        }

    };
}();

jQuery(document).ready(function() {
    FormValidation.init();
});
