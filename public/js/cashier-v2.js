var ItemAutoComplete = $(function() {
    if($('#add_detail_item').length) {
        var cache = {};
        $("#add_detail_item").autocomplete({
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
                var price = parseInt(ui.item.nm_price);
                if($('#member_trans').length) {
                    price = parseInt(ui.item.m_price);
                }
                $('#add_detail_item_price').text(maskMoney(price));
                $('#add_detail_item_price_val').val(price);
                $(this).val(ui.item.item_name);
                $(this).prop('disabled', true);
                $('#add_detail_item_id').val(ui.item.item_id);
                $('.item_qty2').trigger('change');
                return false;
            }
        })
        .autocomplete("instance")._renderItem = function( ul, data ) {
            var price = parseInt(data.nm_price);
            if($('#member_trans').length) {
                price = parseInt(data.m_price);
            }
            return $("<li>")
            .append("<div><b>" + data.item_id + "</b> | " + data.item_name + " | "+ maskMoney(price) + "</div>")
            .appendTo( ul );
        };
    }
    else {
        return false;
    }

});

var MemberAutoComplete = $(function() {
    if($('#member').length) {
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
                $('#member_selected_name').text(ui.item.full_name);
                $('#member_selected_phone').text(ui.item.phone);
                $('#member_selected_address').text(ui.item.address);
                $('#member_selected').text(ui.item.member_id);
                $('#add_trans_member').val(ui.item.member_id);
                $('#member').val(ui.item.member_id + ' ' + ui.item.full_name);
                $('#member').prop('disabled', true);
                $('#panel_confirmation').show();
                return false;
            }
        })
        .autocomplete("instance")._renderItem = function( ul, data ) {
            return $("<li>")
            .append("<div><b>" + data.member_id + "</b> | " + data.full_name + "</div>")
            .appendTo( ul );
        };

        $('#reset').click(function(){
            $('#member_selected_name').text('');
            $('#member_selected_phone').text('');
            $('#member_selected_address').text('');
            $('#member_selected').text('');
            $('#add_trans_member').val('');
            $('#member').val('');
            $('#member').prop('disabled', false);
            $('#panel_confirmation').hide();
        });

        $('#btn_add_trans_member').click(function(){
            bootbox.dialog({
                title: 'Harap tunggu. Jangan tutup halaman ini.',
                message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
            });
            $('#add_trans_type').val('1');
            $('#form_add_trans').submit();
        });
    }
    else {
        return false;
    }
});

var Guest = $(function() {
    if($('#guest').length) {
        $('#reset').click(function(){
            $('#add_trans_guest_name').val('');
            $('#add_trans_guest_phone').val('');
        });

        $('#btn_add_trans_guest').click(function(){
            if($.trim($('#add_trans_guest_name').val()) != '')
            {
                bootbox.dialog({
                    title: 'Harap tunggu. Jangan tutup halaman ini.',
                    message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
                });
                $('#add_trans_type').val('2');
                $('#form_add_trans').submit();
            }
            else
            {
                alert('Nama tamu wajib diisi!');
            }

        });
    }
    else {
        return false;
    }
});

var AddItemTrans = $(function() {
    if($('#form_add_item').length) {
        $('#btn_add_item').click(function(e){
            if($.trim($('#add_detail_item_id').val()) != '')
            {
                $item_id = $.trim($('#add_detail_item_id').val());
                if($('#'+$item_id).length)
                {
                    e.preventDefault();
                    alert('Produk sudah ditambahkan, silahkan update yang sudah ada.');
                    return;
                }
                bootbox.dialog({
                    title: 'Harap tunggu. Jangan tutup halaman ini.',
                    message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
                });
                $('#form_add_item').submit();
            }
            else
            {
                e.preventDefault();
                alert('Isi item yang mau ditambahkan.');
            }

        });

        $('#btn_update_item').click(function(e){
            e.preventDefault();
            //validate discount
            if(parseInt($('#discount').val()) > 100 && $.trim($('#discount_type').val()) == '%') {
                alert('Dikon persen tidak dapat melebihi 100 persen');
                $('#discount').val(0);
                $('#discount').trigger('change');
                return false;
            }
            $('#form_update_item').submit();
        });
        $('.item_qty').change(function(e){
            $item_qty = parseInt($(this).val());

            if($item_qty<1 || $.trim($(this).val()) == '')
            {
                $(this).val(1);
                $(this).trigger('change');
                return;
            }
            $price = $(this).data('price');
            $new_sub_total = $price * $item_qty;
            $('#'+$(this).data('sub-total')).text(maskMoney($new_sub_total));
            calculateTotal();
        });

        $('.item_qty2').change(function(e){
            $item_qty = parseInt($(this).val());

            if($item_qty<1 || $.trim($(this).val()) == '')
            {
                $(this).val(1);
                $(this).trigger('change');
                return;
            }
            $price = $('#add_detail_item_price_val').val();
            $new_sub_total = $price * $item_qty;
            $('#add_detail_sub_total_price').text(maskMoney($new_sub_total));
        });

        $('.delete_row').click(function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
            calculateTotal();
        });

        $('.next_step').click(function(e){
            e.preventDefault();
            if($('.delete_row').length)
            {
                $next_status = $(this).data('trans-set-to');
                $('#trans_set_to').val($next_status);
                $('#form_next_step').submit();
            }
            else
            {
                alert('Pastikan transaksi ini memiliki minimal satu item.')
            }

        });

        $('#discount').change(function(){
            if($.trim($('#discount').val()) == '') {
                $('#discount').val(0);
            }
            calculateTotal();
        });

        $('#discount_type').change(function(){
            calculateTotal();
        });
        function calculateTotal()
        {
            $new_grand_total = 0;
            $('.item_qty_already').each(function(index){
                $item_qty = parseInt($(this).val());
                $price = $(this).data('price');
                $new_sub_total = $price * $item_qty;
                $new_grand_total = $new_grand_total + $new_sub_total;
            });

            $('#grand_total').text(maskMoney($new_grand_total));

            $discount = parseInt($('#discount').val());
            if($discount>0) {
                $discount_type = $.trim($('#discount_type').val());
                if($discount_type == '%') {
                    $discount = $discount/100*$new_grand_total;
                }
                $new_grand_total = $new_grand_total - $discount;
            }
            $('#grand_discount').text(maskMoney($discount));
            $('#grand_total_akhir').text(maskMoney($new_grand_total));
            return $new_grand_total;
        }
    }
    else {
        return false;
    }
});

var Payment = $(function(){
    $(".mask-money").maskMoney({
        thousands:'.',
        allowZero: false,
        precision:0
    });

    if($('#form_finish_trans').length) {
        $('#lunas').click(function(){
            if($('input[name="lunas"]').is(':checked')) {
                $('#paid_value').val(maskMoney($('#total_fix').val()));
                $('#paid_value').prop('disabled', true);
            }
            else {
                $('#paid_value').val('');
                $('#paid_value').prop('disabled', false);
            }
        });

        function calculateChange()
        {
            $lunas = $('input[name="lunas"]').is(':checked');
            $change = 0;
            $total_paid = $.trim($('#total_paid').val());
            if($lunas){
                if($total_paid!='') {
                    $change = unmaskMoney($total_paid) - parseInt($('#total_fix').val());
                }
            }
            else {
                $paid_value = $.trim($('#paid_value ').val());
                if($paid_value!='') {
                    $change = unmaskMoney($total_paid) - unmaskMoney($paid_value);
                }
            }
            $('#change').text(maskMoney($change));
        }

        $('#btn_finish').click(function(e){
            e.preventDefault();
            if($('#payment_type').val()=='') {
                alert('Pilih tipe pembayaran!');
                return;
            }
            $total_fix = parseInt($('#total_fix').val());
            $total_paid = $.trim($('#total_paid').val());

            $lunas = $('input[name="lunas"]').is(':checked');
            if(!$lunas) {
                $paid_value = $.trim($('#paid_value').val());
                if($paid_value==''){
                    alert('Masukkan Total yang ingin dibayarkan!');
                    return;
                }
            }
            if($total_paid=='') {
                alert('Masukkan Total yang dibayarkan!');
                return;
            }
            $total_paid = unmaskMoney($total_paid);
            $kembalian = 0;
            if($lunas) {
                if($total_paid<$total_fix) {
                    alert('Pembayaran lunas minimal '+maskMoney($total_fix));
                    return;
                }
            }
            else {
                $paid_value = unmaskMoney($paid_value);
                if($total_paid<$paid_value) {
                    alert('Total yang dibayarkan tidak cukup!');
                    return;
                }
            }
            calculateChange();
            $('#form_finish_trans').submit();
        });

        $('#paid_value').change(function(){
            calculateChange();
        });

        $('#total_paid').change(function(){
            calculateChange();
        });
    }
    else {
        return false;
    }
});

jQuery(document).ready(function() {
    MemberAutoComplete.init();
    Guest.init();
    AddItemTrans.init();
    Payment.init();
    $('#add_trans').click(function(){
        bootbox.prompt({
            title: "Transaksi untuk?",
            inputType: 'select',
            inputOptions: [
                {
                    text: 'Member',
                    value: '1',
                },
                {
                    text: 'Guest (Non-Member)',
                    value: '2',
                },
            ],
            callback: function (result) {
                if(result)
                {
                    $('#add_trans_type').val(result);
                    $('#form_add_trans').submit();
                }
            }
        });
    });


    $('.number_only').keydown(function (e) {
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
