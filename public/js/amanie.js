function replace_all(find, replace_with, string_source)
{
    return string_source.split(find).join(replace_with);
}

function unmaskMoney(number)
{
    return parseInt(replace_all('.','',$.trim(number)));
}
function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Drew Noakes
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
function maskMoney(number)
{
    return number_format(number, 0, ',', '.');
}
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

var DatePicker = function() {
    var datePicker = function() {
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            yearRange: "1900:{{'2046'}}"
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            datePicker();
        }

    };
}();

var Bootbox = function() {
    var bootboxConfirmation = function() {
        $('.bootbox-confirmationV2').click(function(e){
            var current_link = $(this);
            e.preventDefault();
            var button_class = 'btn-success';
            var btn_confirm_label = $.trim(current_link.html());

            if(current_link.attr('data-btn-label')) {
                btn_confirm_label = current_link.attr('data-btn-label');
            }
            if(current_link.attr('data-confirm-type') =='1') {

            }
            else if(current_link.attr('data-confirm-type') =='0') {
                button_class = 'btn-danger';
            }


            bootbox.confirm({
                message: current_link.attr('data-message'),
                buttons: {
                    confirm: {
                        label: btn_confirm_label,
                        className: button_class
                    },
                    cancel: {
                        label: 'Batal',
                    }
                },
                callback: function(result){
                    if(result) {
                        location.href = current_link.attr('href');
                    }
                /* result is a boolean; true = OK, false = Cancel*/
                }
            })
        });
    }
    return {
        init: function() {
            bootboxConfirmation();
        }
    };
}();

function isInputEmpty(id)
{
    return $.trim($(id).val()) == '';
}

function resetInput(id)
{
    $(id).val('');
    if($(id).prop('disabled')) {
        $(id).prop('disabled',false);
    }
}

$(document).ready(function() {
    $('.submit-button').click(function(e) {
        e.preventDefault();
        $form=$(this).parents('form');
        validateForm($form);
    });

    $('.bootbox-confirmation').click(function(e){
        var current_link = $(this);
        e.preventDefault();
        // bootbox.confirm(current_link.attr('data-message'), function(result) {
        //     if(result) {
        //         location.href = current_link.attr('href');
        //     }
        // });

        bootbox.confirm({
            message: current_link.attr('data-message'),
            buttons: {
                confirm: {
                    label: 'Iya',
                    className: 'purple-rev'
                },
                cancel: {
                    label: 'Batal',
                }
            },
            callback: function(result){
                if(result) {
                    location.href = current_link.attr('href');
                }
            /* result is a boolean; true = OK, false = Cancel*/
            }
        })
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
        if(class_checked){

        }
        $(class_checked).each(function(){
            if($.trim($(this).val()) == '' || $.trim($(this).val()) == '0') {
                $(this).addClass('input-error');
                input_error = true;
            }
            else{
                $(this).removeClass('input-error');
            }
        });

        if($(this).data('form-id')) {
            $form = $($(this).data('form-id'));
            if(!input_error) {
                validateForm($form);
            }
            return;
        }
        $form=$(this).parents('form');

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
                else if(data.redirect_to) {
                    if(data.message) {
                        bootbox.alert(data.message, function(){
                            window.location.replace(data.redirect_to);
                        });
                    }
                    else
                        window.location.replace(data.redirect_to);
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
                    console.log('reset');
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
                else if(data.message_box) {
                    bootbox.dialog({
						message: data.message_box,
						buttons: {
					        confirm: {
					            label: 'OK',
					            className: 'purple-rev'
					        },
						},
						backdrop: true,
					});
                }
                else {
    	            $('.general-error').html(data.message);
    				// App.scrollTo('.general-error', -200);
                }
			}
			// console.log(data);
		},
	});
}

function resetForm ($form)
{
	$form.find('input').not(':input[type=hidden], :input[type=radio]').val('');
	$form.find('textarea').val('');
	$form.find('select').val('');
	$form.find('select').trigger('change');
	App.scrollTo($form, -200);
}
