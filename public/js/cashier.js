$(function() {
    var cache = {};
    $("#items").autocomplete({
        minLength: 3,

        source: function( request, response ) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return;
            }
            $.getJSON($('#get_items').val(), request, function( data, status, xhr ) {
                    cache[term] = data;
                    response(data);
                });
        },
        focus: function( event, data ) {
            // alert("focus");
            return false;
        },
        select: function( event, ui ) {
            hideDefault();
            if(ui.item.item_type == 1) {
                if(ui.item.branch_stock==null || ui.item.branch_stock.stock == 0) {
                    alert('stok tidak tersedia!');
                    return false;
                }
                else {
                    $('#item_qty').attr('data-max', ui.item.branch_stock.stock);
                }
            }
            if(ui.item.item_type == 2) {
                $('#item_pic').show();
            }
            else if(ui.item.item_type == 3) {
                $('#branch_to_rent').show();
                $('#date_to_rent').show();
            }
            var price = ui.item.m_price;
            if($('input[name="member_type"]:checked').val() == "umum") {
                price = ui.item.nm_price;
            }
            $('#items').val( ui.item.item_name);
            $('#items').prop('disabled', true);
            $('#item_selected').val(ui.item.item_id);
            // console.log(price);
            $('#item_selected_price').val(price);
            // console.log(price);
            // $('#item_qty').val('1');
            // $('#item_qty').trigger('change');
            return false;
        }
    })
    .autocomplete("instance")._renderItem = function( ul, data ) {
        var price = parseInt(data.m_price);
        if($('input[name="member_type"]:checked').val() == "umum") {
            price = parseInt(data.nm_price);
        }
        var stock = '';
        if(data.item_type == 1) {
            stock = ' | Stok tersedia: ';
            if(data.branch_stock==null) {
                console.log(data);
                stock = stock + '0';
            }
            else {
                stock = stock + data.branch_stock.stock;
            }
        }
        return $("<li>")
        .append("<div><b>" + data.item_id + "</b> | " + data.item_name + " | "+ number_format(price, 0, ',', '.') + stock + "</div>")
        .appendTo( ul );
    };
});

$(function() {
    var cache2 = {};
    $('#item_pic').autocomplete({
        minLength: 3,

        source: function(request, response) {
            var term2 = request.term;
            if (term2 in cache2) {
                response(cache2[term2]);
                return;
            }

            $.getJSON($('#get_pic').val(), request, function( data, status, xhr ) {
                    cache2[term2] = data;
                    response( data );
                });
        },
        focus: function(event, data) {
            return false;
        },
        select: function( event, ui ) {
            $('#item_pic').val(ui.item.full_name);
            $('#item_pic').prop('disabled', true);
            $('#pic_selected').val(ui.item.employee_id);
            return false;
        }
    })
    .autocomplete("instance")._renderItem = function( ul, data ) {
        return $("<li>")
        .append("<div><b>" + data.employee_id + "</b> | " + data.full_name + "</div>")
        .appendTo( ul );
    };
});

$(function() {
    var cache_member = {};
    $('#member').autocomplete({
        minLength: 3,

        source: function(request, response) {
            var term_member = request.term;
            if (term_member in cache_member) {
                response(cache_member[term_member]);
                return;
            }

            $.getJSON($('#get_members').val(), request, function( data, status, xhr ) {
                    cache_member[term_member] = data;
                    response( data );
                });
        },
        focus: function(event, data) {
            return false;
        },
        select: function( event, ui ) {
            $('#member_selected').val(ui.item.member_id);
            $('#member').val(ui.item.member_id + ' ' + ui.item.full_name);
            $('#member').prop('disabled', true);
            $('#lbl_name').html(ui.item.full_name);
            $('#member_temp').val(ui.item.member_id);
            return false;
        }
    })
    .autocomplete("instance")._renderItem = function( ul, data ) {
        return $("<li>")
        .append("<div><b>" + data.member_id + "</b> | " + data.full_name + "</div>")
        .appendTo( ul );
    };
});

$(function() {
    $('#branch_to_rent').autocomplete({
        source: function(request, response) {
            if($.trim($('#date_to_rent').val())=='') {
                alert('Pilih tanggal sewa terlebih dahulu!');
                $('#date_to_rent').val('');
            }
            else {
                $.getJSON($('#get_branches').val()+$('#item_selected').val()+'.'+$('#date_to_rent').val(), request, function( data, status, xhr ) {
                        if(data.length==0) {
                            if($.trim($('#branch_to_rent').val()) == '')
                                alert('Item tidak tersedia di cabang manapun pada tanggal tersebut.');
                            else
                                alert('Tidak ditemukan, coba cabang lain.');
                        }
                        else {
                            response(data);
                        }
                    });
            }
        },
        focus: function(event, data) {
            return false;
        },
        select: function( event, ui ) {
            $('#branch_to_rent').val(ui.item.branch_name);
            $('#branch_to_rent_selected').val(ui.item.branch_id);
            $('#branch_to_rent').prop('disabled', true);
            return false;
        }
    })
    .autocomplete("instance")._renderItem = function( ul, data ) {
        return $("<li>")
        .append("<div><b>" + data.branch_name + "</b> | Stok tersedia: "+ data.branch_available+"</div>")
        .appendTo( ul );
    };
});



function hideDefault()
{
    $('#item_pic').hide();
    $('#branch_to_rent').hide();
    $('#date_to_rent').hide();
}

$(document).ready(function() {
    $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        yearRange: "2017:2020"
    });

    $('#branch_to_rent').click(function(){
        if($.trim($('#date_to_rent').val())) {

        }
    });
    $('input[name="member_type"]').change(function() {
        if($('input[name="member_type"]:checked').val() == "umum") {
            $('#member').hide();
            $('#lbl_name').html('Umum');
        }
        else {
            $('#member').show();
            $('#lbl_name').html('-');
        }
        $('#btn_reset').trigger('click');
        $('#detail_transaction').html('');
        resetInput('#member_selected');
        resetInput('#member');
        resetInput('#total_temp');$('#total_temp').val(0);
        resetInput('#discount_temp');$('#discount_temp').val(0);
        $('#end_total_temp').val(0);
        $('#discount_total_temp').val(0);
        $('#discount_total_type_temp').val(0);
        $('#discount_total_fixed_temp').val(0);
        $('#others_temp').val(0);
        $('#total_paid_temp').val(0);
        $('#member_temp').val('');
        $('#footer_total').html(0);
        $('#footer_discount').html(0);
        $('#footer_end_total').html(0);
        $('#footer_total_paid').html(0);
        $('#footer_change').html(0);
    });

    $('input[name="discount_type"]').change(function() {
        $('#item_qty').trigger('change');
    });

    $('input[name="discount_total_type"]').change(function() {
        $('#discount_total').trigger('change');
    });


    $('#item_discount').change(function(){
        $('#item_qty').trigger('change');
    });

    // $('#calculate_end_total').click(function(){
    //     if(!isInputEmpty('#discount_total')) {
    //         var discount = parseInt($('#discount_total').val());
    //         var end_total_now = parseInt($('#end_total_temp').val());
    //         $('#discount_total_temp').val(discount);
    //
    //         if($('input[name="discount_total_type"]:checked').val() == "persen") {
    //             discount = discount/100*end_total_now;
    //         }
    //     }
    // });

    $('#others').change(function() {
        if($.trim($(this).val())!='')
            $('#others_temp').val(parseInt($(this).val()));
        else
            $('#others_temp').val(0);
        $('#discount_total').trigger('change');
        $('#total_paid').trigger('change');
    });

    $('#discount_total').change(function(){
        var new_potongan = parseInt($('#discount_temp').val());
        var new_total_akhir = parseInt($('#end_total_temp').val());
        $('#discount_total_type_temp').val($('input[name="discount_total_type"]:checked').val());
        if($.trim($(this).val()) == '' || $.trim($(this).val()) == '0') {
            $('#discount_total_temp').val('0');
            $('#discount_total_fixed_temp').val('0');
        }
        else {
            var discount_input = parseInt($(this).val());
            $('#discount_total_temp').val(discount_input);
            if($('input[name="discount_total_type"]:checked').val()=="persen") {
                var nilaiPastiDiskon = discount_input/100*new_total_akhir;
                $('#discount_total_fixed_temp').val(nilaiPastiDiskon);
                new_total_akhir = new_total_akhir-nilaiPastiDiskon;
                new_potongan = new_potongan+nilaiPastiDiskon;
            }
            else {
                console.log('hehehehe'); console.log(discount_input);
                $('#discount_total_fixed_temp').val(discount_input);
                new_total_akhir = new_total_akhir-discount_input;
                new_potongan = new_potongan+discount_input;
            }
        }
        var others_temp = parseInt($('#others_temp').val());
        new_total_akhir = new_total_akhir + others_temp;
        $('#footer_discount').html(number_format(new_potongan, 0, ',', '.'));
        $('#footer_others').html(number_format(others_temp, 0, ',', '.'));
        $('#footer_end_total').html(number_format(new_total_akhir, 0, ',', '.'));
    });

    $('#item_qty').change(function(){
        var item_price_total = 0;
        if(!isInputEmpty('#item_selected_price')) {
            item_price_total = parseInt($('#item_selected_price').val())*parseInt($('#item_qty').val());
            // console.log(item_price_total);
        }

        if(!isInputEmpty('#item_discount')) {
            var discount = parseInt($('#item_discount').val());
            if(discount>0) {
                if($('input[name="discount_type"]:checked').val() == "persen") {
                    var persentaseBayar = 100-discount;
                    item_price_total = persentaseBayar/100*item_price_total;
                }
                else {
                    item_price_total = item_price_total-discount;
                }
            }
        }

        $('#item_price_total').html(number_format(item_price_total, 0, ',', '.'));
    });

    $('#btn_add').click(function(){
        $('#form_add_item').find('.general-error').html('');
        if(isInputEmpty('#items') || isInputEmpty('#item_qty')) {
            $('#form_add_item').find('.general-error').html('Lengkapi data wajib [yg terdapat tanda *]');
            return;
        }
        else if($('#item_pic').css('display') == "block" && isInputEmpty('#item_pic')) {
            $('#form_add_item').find('.general-error').html('Harap isi PIC');
            return;
        }

        var container = $('#item_selected').val();
        var item_price_total = 0;
        if(!isInputEmpty('#item_selected_price')) {
            item_price_total = parseInt($('#item_selected_price').val())*parseInt($('#item_qty').val());
            // console.log(item_price_total);
        }

        //set-detail
        var id_container = '#' + container;
        if($(id_container).length==0) {
            var clone_temp = $('#temp_detail').clone(true);
            clone_temp.find('.btn_remove').attr('data-item-id',id_container);
            clone_temp.attr('id', container);
            var price_per_item = number_format(parseInt($('#item_selected_price').val()), 0, ',', '.');
            clone_temp.find('.item_name').html($('#items').val() +' | <i>@'+price_per_item+'</i>');
            clone_temp.find('.item_qty').html($('#item_qty').val());
            clone_temp.find('.item_total_price').html(number_format(item_price_total, 0, ',', '.'));
            clone_temp.find('.btn_remove').attr('data-item-total-price', item_price_total);
            clone_temp.show();
            $('#detail_transaction').append(clone_temp);
        }
        else {
            var qty_before = parseInt($(id_container).find('.item_qty').html());
            var new_qty = qty_before + parseInt($('#item_qty').val());
            var item_total_price_before_string = $(id_container).find('.item_total_price').html();
            var item_total_price_before = parseInt(item_total_price_before_string.split('.').join(''));
            var new_item_price_total = item_price_total + item_total_price_before;
            $(id_container).find('.item_qty').html(new_qty);
            $(id_container).find('.item_total_price').html(number_format(new_item_price_total, 0, ',', '.'))
            $(id_container).find('.btn_remove').attr('data-item-total-price', new_item_price_total);
        }
        //set input
        var new_detail = $('#item_selected').val() + "|" +
            parseInt($('#item_selected_price').val()) + "|"  +
            $('#item_qty').val() + "|" +
            $('#item_discount').val() + "|" +
            $('input[name="discount_type"]:checked').val() + "|";

        //set-footer
        var total = parseInt($('#total_temp').val());
        var potongan = parseInt($('#discount_temp').val());
        // var total_akhir = parseInt($('#end_total_temp').val());

        var new_total = total + item_price_total;
        $('#total_temp').val(new_total);

        var new_potongan = 0;
        if(!isInputEmpty('#item_discount')) {
            var discount = parseInt($('#item_discount').val());
            if(discount>0) {

                if($('input[name="discount_type"]:checked').val() == "persen") {
                    new_potongan = discount/100*item_price_total;
                }
                else {
                    new_potongan = discount;
                }
            }
        }

        var item_discount_before = parseInt($(id_container).find('.btn_remove').attr('data-item-discount'));
        $(id_container).find('.btn_remove').attr('data-item-discount', new_potongan+item_discount_before);

        new_detail = new_detail+new_potongan + "|";
        new_detail = new_detail+
                        $('#pic_selected').val() + "|" +
                        $('#date_to_rent').val() + "|" +
                        $('#branch_to_rent_selected').val();

        var temp_input = $('#temp_input').clone(true);
        temp_input.removeAttr('id');
        temp_input.addClass(container);
        temp_input.val(new_detail);
        $(id_container).append(temp_input);
        new_potongan = new_potongan+potongan;
        $('#discount_temp').val(new_potongan);

        var new_total_akhir = new_total-new_potongan;
        $('#end_total_temp').val(new_total_akhir);
        // $('#discount_total').trigger('change');

        $('#footer_total').html(number_format(new_total, 0, ',', '.'));
        // $('#footer_discount').html(number_format(new_potongan, 0, ',', '.'));
        // $('#footer_end_total').html(number_format(new_total_akhir, 0, ',', '.'));
        $('#discount_total').trigger('change');
        // console.log(new_total_akhir);
        $('#btn_reset').trigger('click');
        $('#total_paid').trigger('change');
    });

    $('#total_paid').change(function(){
        if(isInputEmpty('#total_paid')) {
            // console.log('oke');
            return;
        }
        var total_fix = getTotalFix();
        var total_paid = parseInt($('#total_paid').val());
        var kembalian = total_paid - total_fix;
        $('#footer_total_paid').html(number_format(total_paid, 0, ',', '.'));
        $('#footer_change').html(number_format(kembalian, 0, ',', '.'));
        $('#total_paid_temp').val(total_paid);
        validatePayment();
    });

    function getTotalFix()
    {
        var total_fix = parseInt($('#end_total_temp').val());
        if(!isInputEmpty('#others_temp')) {
            total_fix = total_fix + parseInt($('#others_temp').val());
        }
        if(!isInputEmpty('#discount_total_fixed_temp')) {
            total_fix = total_fix - parseInt($('#discount_total_fixed_temp').val());
        }
        return total_fix;
    }

    $('#payment_type').change(function(){
        $('#total_paid').val('');
        $('#total_paid_temp').val('0');
        $('#payment_type_temp').val($.trim($('#payment_type').val()));
        if($.trim($('#payment_type').val()) == '1' || $.trim($('#payment_type').val()) == '2' ) {
            $('#total_paid').show();
        }
        else {
            $('#total_paid').hide();
        }
    });

    function validatePayment()
    {
        $('#form_final').find('.general-error').html('');
        if($.trim($('#payment_type').val()) == '') {
            $('#form_final').find('.general-error').html('Harap pilih tipe pembayaran.');
            return false;
        }
        else if($.trim($('#payment_type').val()) == '1') {
            var total_fix = getTotalFix();
            if(parseInt($('#total_paid_temp').val()) < total_fix) {
                $('#form_final').find('.general-error').html('Jumlah yang dibayarkan kurang.');
                console.log(total_fix);
                return false;
            }
        }
        else if($.trim($('#payment_type').val()) == '3' || $.trim($('#payment_type').val()) == '4') {
            var total_fix = getTotalFix();
            $('#total_paid_temp').val(total_fix);
        }
        return true;
    }

    $('.btn_remove').click(function(){
        var total = parseInt($('#total_temp').val());
        var potongan = parseInt($('#discount_temp').val());
        var new_total = total - parseInt($(this).attr('data-item-total-price'));
        var new_potongan = potongan - parseInt($(this).attr('data-item-discount'));
        $('#footer_total').html(number_format(new_total, 0, ',', '.'));
        $('#total_temp').val(new_total);
        $('#footer_discount').html(number_format(new_potongan, 0, ',', '.'));
        $('#discount_temp').val(new_potongan);
        var new_total_akhir = new_total-new_potongan;
        $('#footer_end_total').html(number_format(new_total_akhir, 0, ',', '.'));
        $('#end_total_temp').val(new_total_akhir);
        $('#discount_total').trigger('change');
        $($(this).attr('data-item-id')).remove();
    });

    $('#btn_process').click(function(){
        if($('input[name="member_type"]:checked').val()=="member" && $('#member').prop('disabled') == false) {
            $('.general-error').html('Harap masukkan member.');
            return;
        }
        if(validatePayment()) {
            $('#form_final').find('.general-error').html('');
            if($.trim($('#payment_type').val()) == '') {
                $('#form_final').find('.general-error').html('Lengkapi data wajib [yg terdapat tanda *]');
                return;
            }

            if($.trim($('#payment_type').val()) =='1' || $.trim($('#payment_type').val()) =='2') {
                if($.trim($('#total_paid').val()) == '') {
                    $('#form_final').find('.general-error').html('Lengkapi data wajib [yg terdapat tanda *]');
                    return;
                }
            }

            $('.submit-button').trigger('click');
        }
        return;
    });

    $('#btn_reset').click(function(){
        resetInput('#items');
        resetInput('#item_selected');
        resetInput('#item_selected_price');
        resetInput('#item_pic');
        resetInput('#date_to_rent');
        resetInput('#branch_to_rent');
        resetInput('#pic_selected');
        resetInput('#item_qty');
        resetInput('#item_discount');
        hideDefault();
        $('#item_price_total').html(0);
    });

    $('#btn_add_item').click(function(){
        $('#form_add_item').show();
        $('#form_final').hide();
    });

    $('#btn_final').click(function(){
        $('#form_final').show();
        $('#form_add_item').hide();
    });

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
});
