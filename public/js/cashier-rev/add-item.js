var AddItemTrans = function() {
    return {
        init: function() {
            var temp_fixed_sub_total = 0;
            $('#btn_add_item').click(function(e){
                e.preventDefault();
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
                    $new_row = $('#tr_temp').clone(true);
                    $new_row.removeAttr('id');
                    $html_string = $new_row.prop('outerHTML');
                    $html_string = $html_string.replace(/--replace_id--/g, $item_id);
                    $html_string = $html_string.replace(/--replace_qty--/g, $.trim($('#add_detail_qty').val()));
                    $html_string = $html_string.replace(/--replace_unmask_price--/g, $.trim($('#add_detail_item_price_val').val()));
                    $html_string = $html_string.replace(/--replace_name--/g, $.trim($('#add_detail_item').val()));
                    $html_string = $html_string.replace(/--replace_price_per_qty--/g, $.trim($('#add_detail_item_price').text()));
                    $html_string = $html_string.replace(/--replace_sub_total--/g, $.trim($('#add_detail_sub_total_price').text()));

                    $html_append = $.parseHTML($html_string);
                    $('#no_item').remove();
                    $('#items_body').append($html_append);
                    $('#'+$item_id).closest('tr').find('.item_qty_already').trigger('change');
                    $('#reset_add_item').trigger('click');
                    MaskMoney.init();
                }
                else
                {
                    alert('Isi item yang mau ditambahkan.');
                }

            });
            $('#reset_add_item').click(function(e){
                e.preventDefault();
                $('#add_detail_item').prop('disabled', false);
                $('#add_detail_item').val('');
                $('#add_detail_item_id').val('');
                $('#add_detail_item_price').text('0');
                $('#add_detail_qty').val(1);
                $('#add_detail_sub_total_price').text('0');
            });

            $('#reset_add_item_cos').click(function(e){
                e.preventDefault();
                $('#add_detail_item_cos').val('');
                $('#add_detail_qty_cos').val(1);
                $('#add_detail_price_cos').val(0);
                $('#add_detail_sub_total_price_cos').text('0');
            });
            $('#btn_add_costumize_item').click(function(e){
                e.preventDefault();
                if($('#add_detail_item_id_cos').length == 0)
                {
                    alert('Terjadi kesalahan!');
                    location.reload();
                    return;
                }
                if($.trim($('#add_detail_item_cos').val()) != '')
                {
                    $price = $.trim($('#add_detail_price_cos').val());
                    if($price == '' || $price == '0')
                    {
                        alert('Isi harga item yang mau ditambahkan.');
                        return;
                    }
                    $item_id = $.trim($('#add_detail_item_id_cos').val());
                    if($('#'+$item_id).length || $item_id == '')
                    {
                        alert('Terjadi kesalahan!');
                        location.reload();
                        return;
                    }
                    // bootbox.dialog({
                    //     title: 'Harap tunggu. Jangan tutup halaman ini.',
                    //     message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
                    // });
                    $new_row = $('#tr_temp').clone(true);
                    $new_row.removeAttr('id');
                    $html_string = $new_row.prop('outerHTML');
                    $html_string = $html_string.replace(/--replace_id--/g, $item_id);
                    $html_string = $html_string.replace(/--replace_qty--/g, $.trim($('#add_detail_qty_cos').val()));
                    $html_string = $html_string.replace(/--replace_unmask_price--/g, unmaskMoney($.trim($('#add_detail_price_cos').val())));
                    $html_string = $html_string.replace(/--replace_name--/g, $.trim($('#add_detail_item_cos').val()));
                    $html_string = $html_string.replace(/--replace_price_per_qty--/g, $.trim($('#add_detail_price_cos').val()));
                    $html_string = $html_string.replace(/--replace_sub_total--/g, $.trim($('#add_detail_sub_total_price_cos').text()));

                    $html_append = $.parseHTML($html_string);
                    $('#no_item').remove();
                    $('#items_body').append($html_append);

                    $append_id = $('#hidden_temp').clone(true);
                    $append_id.removeAttr('id');
                    $append_id.attr('name', 'item_name_'+$item_id);
                    $append_id.val($.trim($('#add_detail_item_cos').val()));
                    $append_price = $('#hidden_temp').clone(true);
                    $append_price.removeAttr('id');
                    $append_price.attr('name', 'item_price_'+$item_id);
                    $append_price.val(unmaskMoney($.trim($('#add_detail_price_cos').val())));
                    $('#temp').append($append_id);
                    $('#temp').append($append_price);
                    $('#'+$item_id).closest('tr').find('.item_qty_already').trigger('change');
                    $next_item_id = parseInt($item_id)+1;
                    $('#add_detail_item_id_cos').val($next_item_id);
                    $('#reset_add_item_cos').trigger('click');
                    MaskMoney.init();
                }
                else
                {
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

            $(document).on('change', '.item_discount', function(e){
                $(this).closest('tr').find('.item_qty').trigger('change');
            });

            $(document).on('change', '.item_qty', function(e){
                $item_qty = parseInt($(this).val());

                if($item_qty<1 || $.trim($(this).val()) == '')
                {
                    $(this).val(1);
                    $(this).trigger('change');
                    return;
                }
                $price = $(this).data('price');
                $fixed_sub_total = $new_sub_total = $price * $item_qty;
                $('#'+$(this).data('sub-total')).text(maskMoney($new_sub_total));
                $discount = $.trim($('#'+$(this).data('discount')).val());
                if($discount != '' && $discount != '0') {
                    $discount = unmaskMoney($discount);
                    $discount_type = $.trim($('#'+$(this).data('discount-type')).val());
                    if($discount_type == '%') {
                        if($discount>100) {
                            alert('Diskon dalam persen tidak bisa lebih dari 100%');
                            $('#'+$(this).data('discount')).val(0);
                            $('#'+$(this).data('discount')).trigger('change');
                            return;
                        }
                        $discount = $discount/100 *$new_sub_total;
                    }
                    $fixed_sub_total = $new_sub_total - $discount;
                }
                else {
                    $discount = 0;
                }
                $('#val_'+$(this).data('discount')).text(maskMoney($discount));
                $('#'+$(this).data('fixed-sub-total')).text(maskMoney($fixed_sub_total));
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

            $('.item_qty3').change(function(){
                $item_qty = parseInt($(this).val());

                if($item_qty<1 || $.trim($(this).val()) == '')
                {
                    $(this).val(1);
                    $(this).trigger('change');
                    return;
                }
                calculateCostumizeItem();
            });

            $('#add_detail_price_cos').change(function(){
                calculateCostumizeItem();
            });

            function calculateCostumizeItem()
            {
                $item_qty = parseInt($('.item_qty3').val());
                $price = 0;
                if($('#add_detail_price_cos').val()) {
                    $price = unmaskMoney($('#add_detail_price_cos').val());
                }
                $new_sub_total = $price * $item_qty;
                $('#add_detail_sub_total_price_cos').text(maskMoney($new_sub_total));
            }

            $(document).on('click', '.delete_row', function(e){
                e.preventDefault();
                $(this).closest('tr').remove();
                if($('#items_body').find('tr').length==0) {
                    $('#discount').val(0);
                }
                calculateTotal();
            });

            $('.next_step').click(function(e){
                e.preventDefault();
                $next_status = parseInt($(this).data('trans-set-to'));
                $('#trans_set_to').val($next_status);
                if($next_status == 2)
                {
                    if($('.delete_row').length)
                    {
                        $('#form_next_step').submit();
                    }
                    else
                    {
                        alert('Pastikan transaksi ini memiliki minimal satu item.')
                    }
                }
                else if($next_status == 3)
                {
                    bootbox.confirm({
                        message: "Anda yakin ingin membatalkan transaksi ini?",
                        buttons: {
                            confirm: {
                                label: 'Iya, Batalkan',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: 'Tidak',
                                className: 'btn-default'
                            }
                        },
                        callback: function (result) {
                            if(result) {
                                $('#form_next_step').submit();
                            }
                        }
                    });
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
                $total_item_discount = 0;
                $('#form_update_item').find('.item_qty_already').each(function(index){
                    $item_qty = parseInt($(this).val());
                    $price = $(this).data('price');
                    $new_sub_total = $price * $item_qty;
                    $new_grand_total = $new_grand_total + $new_sub_total;
                    $item_discount = unmaskMoney($('#val_'+$(this).data('discount')).text());
                    $total_item_discount = $total_item_discount + $item_discount;
                });
                $('#total_item_discount').text(maskMoney($total_item_discount));
                $('#grand_total').text(maskMoney($new_grand_total));
                $new_grand_total_2 = $new_grand_total-$total_item_discount
                $('#grand_total_2').text(maskMoney($new_grand_total_2));
                $discount = unmaskMoney($('#discount').val());

                $discount = $.trim($('#discount').val());
                if($discount != '' && $discount != '0') {
                    $discount = unmaskMoney($discount);
                    $discount_type = $.trim($('#discount_type').val());
                    if($discount_type == '%') {
                        if($discount>100) {
                            alert('Diskon dalam persen tidak bisa lebih dari 100%');
                            $('#discount').val(0);
                            $('#discount').trigger('change');
                            return;
                        }
                        $discount = $discount/100*$new_grand_total_2;
                    }
                    $new_grand_total_2 = $new_grand_total_2 - $discount;
                }
                $('#grand_discount').text(maskMoney($discount));
                $('#grand_total_akhir').text(maskMoney($new_grand_total_2));
                validateForm($('#form_update_item'));
            }
        }
    }
}();


jQuery(document).ready(function() {
    AddItemTrans.init();
});
