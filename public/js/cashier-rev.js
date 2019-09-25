var is_loading = false;
function loadingCashier()
{
    is_loading = true;
    bootbox.dialog({
        title: 'Harap tunggu. Jangan tutup halaman ini.',
        message: '<p style="text-align: center"><i class="fa fa-spin fa-spinner"></i> Loading...</p>',
        closeButton: false,
    });
}

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

var BranchAutoComplete = $(function() {
    if($('#branchautocomplete').length) {
        var cache_branch = {};
        $('#branchautocomplete').autocomplete({
            minLength: 3,

            source: function(request, response) {
                var term_branch = request.term;
                if($('#add_trans_branch').length)
                {
                    request.other_term = $('#add_trans_branch').val();
                }

                if (term_branch in cache_branch) {
                    response(cache_branch[term_branch]);
                    return;
                }

                $.getJSON($('#get_branches').val(), request, function( data, status, xhr ) {
                        cache_branch[term_branch] = data;
                        response( data );
                    });
            },
            focus: function(event, data) {
                return false;
            },
            select: function( event, ui ) {
                $('#branch_selected_name').text(ui.item.branch_name);
                $('#branch_selected_phone').text(ui.item.phone);
                $('#branch_selected_address').text(ui.item.address);
                $('#branch_selected').text(ui.item.id);
                $('#add_trans_for_branch').val(ui.item.id);
                $('#branchautocomplete').val('Cabang ' + ui.item.branch_name + '#' + ui.item.id);
                $('#branchautocomplete').prop('disabled', true);
                $('#panel_confirmation').show();
                return false;
            }
        })
        .autocomplete("instance")._renderItem = function( ul, data ) {
            return $("<li>")
            .append("<div><b>Cabang " + data.branch_name + " #"+ data.id +"</b> | " + data.phone + "</div>")
            .appendTo( ul );
        };

        $('#reset').click(function(){
            $('#branch_selected_name').text('');
            $('#branch_selected_phone').text('');
            $('#branch_selected_address').text('');
            $('#branch_selected').text('');
            $('#add_trans_member').val('');
            $('#branchautocomplete').val('');
            $('#branchautocomplete').prop('disabled', false);
            $('#panel_confirmation').hide();
        });

        $('#btn_add_trans_branch').click(function(){
            bootbox.dialog({
                title: 'Harap tunggu. Jangan tutup halaman ini.',
                message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
            });
            $('#add_trans_type').val('3');
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
                console.log(!$lunas);
                if(!$lunas) {
                    $total_fix = parseInt($('#total_fix').val());
                    if(unmaskMoney($paid_value) >= $total_fix) {
                        alert('DP tidak bisa lebih dari sama dengan '+ maskMoney($total_fix));
                        $('#paid_value ').val('');
                        return;
                    }
                }
            }
            $('#change').text(maskMoney($change));
        }
        if($('#btn_finish_with_skip').length)
        {
            $('#btn_finish_with_skip').click(function(e){
                e.preventDefault();
                $('#payment_type').val(1);
                $('input[name="lunas"]').trigger('click');
                $('#form_finish_trans').submit();
            });
        }

        $('.js-qty-done').change(function(e){
            $now_val = parseInt($.trim($(this).val()));
            if($now_val > parseInt($(this).attr('max'))) {
                alert('tidak bisa lebih dari qty');
                $(this).val($(this).attr('max'));
            }
            else if($now_val < 0 ) {
                alert('tidak bisa minus');
                $(this).val($(this).attr('max'));
            }
        });
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
                if($paid_value >= $total_fix) {
                    alert('DP tidak bisa lebih dari sama dengan '+ maskMoney($total_fix));
                    return;
                }
            }
            //asasa
            if($total_fix > 0 && $total_paid=='') {
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
            $submit = true;
            if($('#ke_galeri').length)
            {
                $submit = validateKeGaleri();
            }
            if($submit)
            {
                $('#form_finish_trans').submit();
            }

        });

        $('#paid_value').change(function(){
            calculateChange();
        });

        $('#total_paid').change(function(){
            calculateChange();
        });

        $('#ke_galeri').change(function(){
            validateKeGaleri();
        });

        function validateKeGaleri()
        {
            if($.trim($('#ke_galeri').val()) != '')
            {
                $ke_galeri = unmaskMoney($('#ke_galeri').val());
                $total_discount = unmaskMoney($('#total_discount').val());
                if($ke_galeri>$total_discount) {
                    alert('Jumlah yg ditagihkan ke galeri tidak dapat melebihi total diskon');
                    $('#ke_galeri').val('');
                    return false;
                }
                if($ke_galeri<0) {
                    alert('Jumlah yg ditagihkan ke galeri tidak dapat minus');
                    $('#ke_galeri').val('');
                    return false;
                }
            }
            return true;
        }



    }
    else {
        return false;
    }
});

jQuery(document).ready(function() {
    MemberAutoComplete.init();
    BranchAutoComplete.init();
    Guest.init();
    Payment.init();

    $('.btn-search-invoice').click(function(){
        $url = '';
        if($(this).attr('data-for') == 'incentive') {
            $url = $.trim($('#url-incentive').val());
        }
        else if($(this).attr('data-for') == 'pending') {
            $url = $.trim($('#url-check-pending').val());
        }
        else {
            alert('terjadi kesalahan');
            location.reload();
        }
        bootbox.prompt({
            title: "Masukkan Invoice Id atau Trx Id",
            inputType: 'text',
            buttons: {
               confirm: {
                   label: 'Lanjut',
                   className: 'btn purple-rev'
               },
               cancel: {
                   label: 'Batal'
               }
            },
            callback: function (result) {
                if(result) {
                    // console.log($url);
                    loadingCashier();
                    window.location.href = $url + result.split('/').join('-');
                }
            }

        });
    });
    $('#add_trans').click(function(){
        bootbox.prompt({
            title: "Transaksi untuk?",
            inputType: 'select',
            buttons: {
               confirm: {
                   label: 'Lanjut',
                   className: 'btn purple-rev'
               },
               cancel: {
                   label: 'Batal'
               }
            },
            inputOptions: [
                {
                    text: 'Member',
                    value: '1',
                },
                {
                    text: 'Guest (Non-Member)',
                    value: '2',
                },
                {
                    text: 'Antar Cabang',
                    value: '3',
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

    //bagikan incentive
    $('.js-bagikan').click(function(e){
        e.preventDefault();
        if($(this).attr('data-now') == 'hidden') {
            $(this).next('span').css('visibility','unset');
            $(this).attr('data-now', 'unset');
        }
        else {
            $(this).next('span').css('visibility','hidden');
            $(this).attr('data-now', 'hidden');
        }

    });
});
