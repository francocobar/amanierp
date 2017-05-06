var MaskMoney = function() {
    var maskMoney = function(){
        $(".mask-money").maskMoney({
            thousands:'.',
            allowZero: false,
            precision:0
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            maskMoney();
        }

    };
}();

$(document).ready(function() {
    $('.submit-button').click(function(e) {
        e.preventDefault();
        $form=$(this).parents('form');
        validateForm($form);
    });

    $('.bootbox-confirmation').click(function(e){
        var current_link = $(this);
        e.preventDefault();
        bootbox.confirm(current_link.attr('data-message'), function(result) {
            console.log($(this));
            if(result) {
                location.href = current_link.attr('href');
            }
        });
    });

    $('.bootbox-view-note').click(function(e) {
        var current_link = $(this);
        e.preventDefault();

        bootbox.alert({
            size: "small",
            message: current_link.attr('data-message'),
            callback: function(result){ /* result is a boolean; true = OK, false = Cancel*/ },
            buttons: {
                ok: {
                    label: 'Tutup',
                    className: 'btn-default'
                },
            }
        });
    });

    $('.submit-button-validation').click(function(e) {
        e.preventDefault();
        var class_checked = $(this).data('unique');
        var input_error = false;
        $(class_checked).each(function(){
            if($.trim($(this).val()) == '' || $.trim($(this).val()) == '0') {
                $(this).addClass('input-error');
                input_error = true;
            }
            else{
                $(this).removeClass('input-error');
            }
        });
        $form=$($(this).data('form-id'));
        if(!input_error) {
            validateForm($form);
        }

    });

    $('.input-error').change(function(e){
        if($.trim($(this).val()) != '') $(this).removeClass('input-error');
    });

    $(".input-only-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});

function validateForm($form, $function){
    bootbox.dialog({
		message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Loading...</div>',
		closeButton: false,
	});
	$form.find('.general-error').html('');
	$.ajax({
		url:$form.attr('action'),
		method:"POST",
		dataType:'JSON',
  	    async: false,
		data:$form.serializeArray(),
		success:function(data){
			bootbox.hideAll();
			if(data.status=='success') {
				if(data.need_reload) {
					bootbox.alert(data.message, function(){
                        location.reload();
					});
				}
				else {
					bootbox.dialog({
						message: data.message,
						buttons: {
					        confirm: {
					            label: 'Ok',
					            className: 'purple-rev'
					        },
						},
						backdrop: true,
					});
				}
                if(!data.no_reset_form) {
                    resetForm($form);
                }
			}
			else if(data.status=='error') {
                if(data.need_reload) {
					bootbox.alert(data.message, function(){
                        location.reload();
					});
				}
                else if(data.need_login) {
                    bootbox.alert(data.message, function(){
                        window.location.replace('/login');
                    });
                }
                else if(data.redirect_to) {
                    bootbox.alert(data.message, function(){
                        window.location.replace(data.redirect_to);
                    });
                }
                else {
    	            $('.general-error').html(data.message);
    				App.scrollTo('.general-error', -200);
                }
			}
			// console.log(data);
		},
	});
}

function resetForm ($form)
{
	$form.find('input[type!="hidden"]').val('');
	$form.find('textarea').val('');
	$form.find('select').val('');
	$form.find('select').trigger('change');
	App.scrollTo($form, -200);
}
