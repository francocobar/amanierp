// String.prototype.replaceAll = function(search, replacement) {
//     var target = this;
//     return target.replace(new RegExp(search, 'g'), replacement);
// };

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

function unmaskMoney(number)
{
    return parseInt(number.replaceAll('.',''));
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

function hideAllModals()
{
    $('.modal').modal('hide');
}

function loadingWithMessage(message)
{
    bootbox.dialog({
        message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> <span class="blink-me">'+message+'</span></div>',
        closeButton: false,
    });
}

function showErrorMessageByResponseCode(response_code)
{
    if(response_code==401) {
        return showErrorMessage('Unauthorized. <a href="#" class="js-reload">[Refresh halaman]</a>');
    }
    else if(response_code==500) {
        return showErrorMessage('Terjadi kesahlan. <a href="#" class="js-reload">[Coba klik di sini]</a> <br/>*anda mungkin akan diminta login ulang');
    }
    return showErrorMessage('Terjadi kesahlan. <a href="#" class="js-reload">[Refresh halaman]</a> <br/>*anda mungkin akan diminta login ulang');

}

function showErrorMessage(message, closeButton)
{
    bootbox.dialog({
        message: '<div class="text-center" style="color: red;">'+message+'</div>',
        closeButton: closeButton == undefined ? false : closeButton,
    });
    return true;
}

function hideAllBootbox()
{
    bootbox.hideAll();
}

function setFieldsMask()
{
    if($('.js-mask-idr').length > 0) {
        $('.js-mask-idr').maskMoney({prefix:'Rp ', allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
    }

    if($('.js-mask-percent').length > 0) {
        $('.js-mask-percent').maskMoney({suffix:' %', allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
    }
}

function setFieldsDefaultValue($form_selector)
{
    $($form_selector).find('.js-default-empty').val('');
    $($form_selector).find('.js-default-checked').prop('checked', true);
    $($form_selector).find('.js-default-required-n').prop('required', false);
    $($form_selector).find('.js-default-display').show();
    $($form_selector).find('.js-default-display-n').hide();
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.js-hide').click(function(e){
        e.preventDefault();
        if($(this).attr('data-target-hide')) {
            $($(this).attr('data-target-hide')).hide();
        }
    });

    $('.js-set-empty-value').click(function(e){
        e.preventDefault();
        if($(this).attr('data-target-value')) {
            $selector = $($(this).attr('data-target-value'));
            $selector.val('');
            if($selector.data('default-enabled')) {
                $($selector).prop('disabled', false);
            }
        }
    });

    $(document).on('click','.js-reload', function(e){
        e.preventDefault();
        hideAllBootbox();
        loadingWithMessage('Halaman sedang direfresh . . .');
        location.reload();
    });

    $(document).on('click','.js-hide-all-bootbox', function(e){
        e.preventDefault();
        hideAllBootbox();
    });

    $(document).on('click','button.js-go-to-url', function(e){
        e.preventDefault();
        hideAllBootbox();
    });

    setFieldsMask();

    $('.js-mask-percent').keypress(function(e) {
        var value = unmaskMoney($(this).val().replaceAll(' %', ''));
        // value = value.replaceAll('.','');
        // console.log((value));
        if(value > 100) {
            alert('Maksimal potongan 100 %');
            $(this).val('100 %');
        };
    });
    if(typeof yourvar !== 'undefined') {
        toastr.options = {
          "closeButton": false,
          "debug": false,
          "newestOnTop": false,
          "progressBar": false,
          "positionClass": "toast-bottom-full-width",
          "preventDuplicates": true,
          "onclick": null,
          "showDuration": "100",
          "hideDuration": "1000",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        }
    }
});
