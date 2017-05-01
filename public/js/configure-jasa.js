var FormValidation = function () {

    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            $('input [name="penyebut"]').change(function() {
                alert("oke");
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
