@extends('master.master-pos')

@section('content')
    <div class="container marketing">

        <!-- Three columns of text below the carousel -->
        <div class="row">
            <div class="col-lg-6">
                <h2>Buat transaski baru</h2>
                <button id="jsAddMemberTrans" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block" data-toggle="modal" data-target="#jsModalAddMemberTrans">Member</button>
                <button id="jsAddGuestTrans" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block" data-toggle="modal" data-target="#jsModalAddGuestTrans">Guest</button>
                <button id="js-add-branch-trans" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block">Antar Cabang</button>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-6">
                <h2>Transaksi sedang berjalan <a href="#" id="jsRefreshOnGoingTransList"><span class="fa fa-retweet"></span></a></h2>
                <ul id="jsListOngoingTrans" class="list-group">
                    <li id="jsLoadingOngoingTrans" class="list-group-item"><span class="blink-me">Loading . . .</span></li>
                    <li id="jsNoDataOnGoingTrans" class="list-group-item" style="display: none;">Tidak ada transaksi.</li>
                </ul>
            </div><!-- /.col-lg-4 -->
        </div><!-- /.row -->
</div>
@endsection


@section('blade-hidden')
<!-- Modal Add Member Trans-->
<div class="modal fade" id="jsModalAddMemberTrans" role="dialog" aria-labelledby="jsModalAddMemberTrans" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle1">Buat Transaksi Baru - Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input id="jsAutoCompleteMember" data-default-enabled="true" type="text" class="form-control" placeholder="* Nama / Id Member"/>
                    </div>
                </div>
                <div class="row" id="jsPanelPreview" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-light" role="alert">
                            <b>Nomor Hp:</b> <span id="jsPreviewPhoneNumber"></span><br/>
                            <b>Alamat:</b> <span id="jsPreviewAddress"></span>
                            <button data-target-value="#jsAutoCompleteMember" data-target-hide="#jsPanelPreview" id="jsChangeSelectedMember" type="button" class="btn btn-warning btn-lg btn-block js-hide js-set-empty-value" style="margin-top: 8px">Ganti</button>
                        </div>
                        {!! Form::open(['id' => 'jsFormCreateMemberTrans', 'route' => array('cashier.creates-transaction.post',1, request()->branch_id)]) !!}
                            <input type="hidden" name="member" id="jsTransMember" value=""/>
                            <input type="hidden" name="flencry" value="{{$flag_encrypted}}" />
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                <button id="jsBtnCreateMemberTrans" type="button" class="btn btn-primary" disabled>Buat Transaksi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Guest Trans -->
<div class="modal fade" id="jsModalAddGuestTrans" role="dialog" aria-labelledby="jsModalAddMemberTrans" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['id' => 'jsFormCreateGuestTrans', 'route' => array('cashier.creates-transaction.post',2, request()->branch_id)]) !!}
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle2">Buat Transaksi Baru - Guest</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input id="jsGuestName" type="text" class="form-control" name="guest_name" placeholder="* Nama Guest" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input type="text" class="form-control" placeholder="Nomor Hp"/>
                    </div>
                    <input type="hidden" name="flencry" value="{{$flag_encrypted}}" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                <button id="jsBtnCreateGuestTrans" type="submit" class="btn btn-primary">Buat Transaksi</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Modal View Ongoing Transaction -->
<div class="modal fade" id="jsModalViewOngoingTrans" role="dialog" aria-labelledby="jsModalViewOngoingTrans" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsModalTitleOngoingTrans"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="jsViewOngoingTransLoading" class="row">
                        <span class="blink-me" style="margin: 0 auto;">Loading . . .</span>
                </div>
                <div id="jsViewOngoingTransData" class="row">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">&nbsp;</th>
                                <th scope="col">Nama Item</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="jsTempRowItemBody">
                            <tr id="jsTempRowItem" style="display: none;">
                                <th scope="row"><a href="{{route('cashier.remove-item-ongoing-transaction', ['item_code', 'trans_id'])}}" class="js-remove-item">[x]</></th>
                                <td class="js-item-name">...</td>
                                <td>
                                    <input class="js-item-number" type="hidden" name="item_number" value="0" />
                                    <input class="form-control js-item-qty" type="number" name="item_qty_per_row" value="" style="width:72px;"/>
                                </td>
                                <td class="js-item-sub-total-price" style="text-align: right;">0.000.000</td>
                            </tr>
                        </tbody>
                    </table>
                    {!! Form::open(['id' => 'jsUpdateOngoingTrans', 'style'=>'width: 100%;', 'route' => array('cashier.add-item-ongoing-transaction','trans_id')]) !!}
                    <table class="table" style="margin: 0 auto; width: 80%;">
                        <tbody>
                            <tr>
                                <td colspan="3">
                                    <input id="jsSearchItemKeyword" type="text" class="form-control js-clear-selected" name="keyword" value="" placeholder="Keyword: Nama Item / Item Code" />
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%; vertical-align: middle; text-align: center; border-top: 0;">
                                    Quantity
                                </td>
                                <td style="width: 20%; border-top: 0;">
                                    <input data-price="0" type="number" id="jsQty" class="form-control" name="item_qty" value="1" min="1" />
                                </td>
                                <td style="vertical-align: middle; border-top: 0;">
                                    Sub Total: <span style="font-weight: bold" id="jsSubTotalSelectedItem">0</span>
                                </td>
                            </tr>

                            <input type="hidden" name="flencry" value="{{$flag_encrypted}}" />
                            <input type="hidden" name="item_code" id="jsSelectedItemCode" />
                            <tr style="text-align: center;">
                                <td colspan="3" style="border-top: 0;">
                                    <button id="jsBtnResetSelectedItem" type="button" class="btn btn-warning" disabled>Reset</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                                    <button id="jsAddItemOngoingTrans" type="submit" class="btn btn-success" disabled>Tambah Item</button> |
                                    <button id="jsAddSeriesItem" type="submit" class="btn btn-outline-success">Paket Series</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    {!! Form::close() !!}
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="js-item-name">
                                    <div class="input-group mb-3">
                                        <input id="jsCouponCode" type="text" class="form-control" placeholder="Kode Kupon" aria-label="Kode Kupon" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button id="jsApplyCoupon" class="btn" type="button">Apply</button>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <button id="jsBtnDeposit" type="button" class="js-btn-next btn btn-dark" disabled>Ke Deposit</button>
                                    <button id="jsBtnPayment" type="button" class="js-btn-next btn btn-primary" disabled>Ke Pembayaran</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="jsModalRemoveOngoingTransConfirmation"  class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Hapus Transaksi <span id="jsTransToRemove"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Anda yakin menghapus transaksi ini?</p>
        <input type="hidden" id="jsTransIdToRemove" value=""/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
        <button id="jsRemoveThisTrans" type="button" class="btn btn-primary">Iya, Hapus Transaksi Ini</button>
      </div>
    </div>
  </div>
</div>
<div id="jsModalSeriesTransConfirmation"  class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Jadikan Transaksi Paket Series untuk Transaksi <span id="jsTransToSeries"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Transaksi Paket Series <b>HANYA SATU ITEM PER TRANSAKSI</b>, item yang sudah ditambahkan sebelumnya akan terhapus semua
            jika Anda menjadikan transaksi ini sebagai Transaksi Paket Series. Anda yakin?</p>
        <input type="hidden" id="jsTransIdToRemove" value="{{$flag_encrypted}}"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
        <button id="jsAddSeriesItemPage" type="button" class="btn btn-primary">Iya, Jadikan Transaksi Paket Series</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="jsUrlGetMembersAutoComplete" value="{{route('cashier.searches-members')}}" />
<input type="hidden" id="jsUrlGetItemsAutoComplete" value="{{route('cashier.searches-items', array('trans_id', request()->branch_id))}}" />
<input type="hidden" id="jsUrlGetOngoingTransData" value="{{route('cashier.get-ongoing-transaction')}}" />
<input type="hidden" id="jsUrlRemoveOngoingTransData" value="{{route('cashier.remove-ongoing-transaction')}}" />
<input type="hidden" id="jsUrlApplyCoupon" value="{{route('cashier.apply-coupon', 'trans_id')}}" />
<input type="hidden" id="jsUrlUpdateItem" value="{{route('cashier.update-item-ongoing-transaction', 'trans_id')}}" />
<input type="hidden" id="jsUrlPaymentOngoingTrans" value="{{route('cashier.payment-ongoing-transaction', array(request()->branch_id, 'trans_id'))}}" />
<input type="hidden" id="jsUrlDepositOngoingTrans" value="{{route('cashier.deposit-ongoing-transaction', array(request()->branch_id, 'trans_id'))}}" />
<input type="hidden" id="jsUrlPaketSeriesTrans" value="{{route('cashier.series-ongoing-transaction', array(request()->branch_id, 'trans_id'))}}" />
<input type="hidden" id="jsViewedOngoingTransId" value="000" />
{!! Form::open(['id' => 'jsFormGetDataOnGoingTransList', 'route' => array('cashier.ongoing-transactions', request()->branch_id)]) !!}
    <input type="hidden" name="flencry" value="{{$flag_encrypted}}"/>
{!! Form::close() !!}
@endsection

@section('blade-style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
@endsection

@section('blade-script')
<script src="{{ URL::asset('js/cashier-rev/member-auto-complete.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/cashier-rev/item-auto-complete.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#jsBtnPayment').click(function(e){
        loadingWithMessage('Menuju halaman pembayaran . . .');
        window.location.href = $('#jsUrlPaymentOngoingTrans').val().replace('trans_id', $('#jsViewedOngoingTransId').val());
    });
    $('#jsBtnDeposit').click(function(e){
        loadingWithMessage('Menuju halaman deposit . . .');
        window.location.href = $('#jsUrlDepositOngoingTrans').val().replace('trans_id', $('#jsViewedOngoingTransId').val());
    });

    $('#jsAddSeriesItemPage').click(function(e){
        loadingWithMessage('Menuju halaman Paket Series . . .');
        window.location.href = $('#jsUrlPaketSeriesTrans').val().replace('trans_id', $('#jsViewedOngoingTransId').val());
    });

    $('#jsModalAddMemberTrans').on('show.bs.modal', function (e) {
        $('#jsChangeSelectedMember').trigger('click');
    });

    $('#jsModalAddGuestTrans').on('show.bs.modal', function (e) {
        $('#jsGuestName').val('');
        $('#jsBtnCreateGuestTrans').prop('disabled', true);
    });

    $('#jsGuestName').keyup(function(e){
        if($.trim($(this).val())=='') {
            $('#jsGuestName').val('');
            $('#jsBtnCreateGuestTrans').prop('disabled', true);
        }
        else {
            $('#jsBtnCreateGuestTrans').prop('disabled', false);
        }
    });

    $('#jsChangeSelectedMember').click(function(e){
        e.preventDefault();
        $('#jsBtnCreateMemberTrans').prop('disabled', true);
        $('#jsTransMember').val('');
    });

    //get all ongoing transactions
    $('#jsRefreshOnGoingTransList').on('click', function(e){
        e.preventDefault();
        $('.js-ongoing-trans-appended').remove();
        $('#jsLoadingOngoingTrans').show();
        $('#jsNoDataOnGoingTrans').hide();
        console.log('get-data-ongoing-transactions');
        $form = $('#jsFormGetDataOnGoingTransList');
        $.ajax({
    		url:$form.attr('action'),
    		method:"POST",
    		dataType:'JSON',
      	    async: false,
    		data:$form.serializeArray(),
    		success:function(response){
                // console.log(response.data);
                if(response.data.length>0) {
                    $.each(response.data, function( key, value ) {
                        $row = '<li class="js-ongoing-trans-appended list-group-item" style="display: none;text-align: left;"><a class="js-view-ongoing-trans" data-trans-id="'+value.id +'" href="#" style="display: block;">'+ value.customer_name+' #' + value.id +'</a> | <a class="js-remove-ongoing-trans" data-trans-id="'+value.id +'" href="#"><span class="badge badge-danger" href="#">Hapus Transaksi</span></a></li>';
                        $('#jsListOngoingTrans').append($row);
                        // console.log(value);
                    });
                    setTimeout(function(){
                        $('#jsLoadingOngoingTrans').hide();
                        $('.js-ongoing-trans-appended').show();
                    }, 1000);
                }
                else {
                    setTimeout(function(){
                        console.log('zzz');
                        $('#jsLoadingOngoingTrans').hide();
                        $('#jsNoDataOnGoingTrans').show();
                    }, 1500);
                }
            }
        });
    });

    //action create trans for member
    $('#jsBtnCreateMemberTrans').on('click', function(e){
        e.preventDefault();
        $form = $('#jsFormCreateMemberTrans');
        $.ajax({
    		url:$form.attr('action'),
    		method:"POST",
    		dataType:'JSON',
      	    async: false,
    		data:$form.serializeArray(),
    		success:function(response){
                if(response.data) {
                    var value = response.data;
                    $row = '<li class="js-ongoing-trans-appended list-group-item" style="display: none;text-align: left;"><a class="js-view-ongoing-trans" data-trans-id="'+value.id +'" href="#" style="display: block;">'+ value.customer_name+' #' + value.id +'</a> | <a class="js-remove-ongoing-trans" data-trans-id="'+value.id +'" href="#"><span class="badge badge-danger" href="#">Hapus Transaksi</span></a></li>';
                    $('#jsListOngoingTrans').append($row);
                    setTimeout(function(){
                        $('#jsLoadingOngoingTrans').hide();
                        $('#jsNoDataOnGoingTrans').hide();
                        hideAllModals();
                        $('.js-ongoing-trans-appended').show();
                    }, 1000);
                }
                else {
                    showErrorMessage('Gagal membuat transaksi <a href="#" class="js-hide-all-bootbox">[Tutup]</a>');
                }
            },
            error: function (request, status, error) {
                showErrorMessageByResponseCode(request.status);
            },
            beforeSend: function() {
                console.log('create-guest-transaction-start');
                hideAllModals();
                loadingWithMessage('Sedang membuat transaksi . . .');
            },
            complete: function() {
                console.log('create-guest-transaction-finish');
                hideAllModals();
            }
        });
    });

    function appendItemRow(value, discount)
    {
        $row = $('#jsTempRowItem').clone(true);
        $row.removeAttr('id');
        $row.show();
        $row.addClass('js-row-item-appended');
        $url_to_remove = $row.find('.js-remove-item').attr('href');
        $row.find('.js-remove-item').attr('href', $url_to_remove.replace('item_code', value.item_code).replace('trans_id', $('#jsViewedOngoingTransId').val()));

        $row.find('.js-item-name').html('#' + value.item_code + ' ' + value.item_name);
        $row.find('.js-item-qty').val(value.item_qty);
        $row.find('.js-item-number').val(value.item_code);
        $row.find('.js-item-qty').attr('data-price', value.item_price);
        $row.find('.js-item-sub-total-price').html(maskMoney(value.item_price * value.item_qty));
        $row.find('.js-item-sub-total-price').attr('data-sub-total-price',(value.item_price * value.item_qty));

        if(discount == undefined) {
            discount = 0;
        }
        $('#jsTempRowItemBody').append($row);
    }
    function calculateGrandTotal()
    {
        var grandTotal = 0;
        $('.js-row-item-appended .js-item-sub-total-price').each(function() {
        // console.log(parseFloat($(this).attr('data-sub-total-price')));
            grandTotal = parseFloat(grandTotal) + parseFloat($(this).attr('data-sub-total-price'));
        });
        $('.js-btn-next').prop('disabled', grandTotal == 0);
        if($('#jsGrandTotal').length > 0) {
            $('#jsGrandTotal').parents('tr').remove();
        }
        if($('#jsDiscountValue').length > 0) {
            $('#jsDiscountValue').parents('tr').remove();
        }
        if(grandTotal > 0) {
            //
            // $('#jsCouponCode').attr('data-coupon', 'Ref#'+coupon.id + ' ' + coupon.coupon_code);
            // $('#jsCouponCode').attr('data-value', parseInt(coupon.disc_value));
            // $('#jsCouponCode').attr('data-value-type', parseInt(coupon.disc_value_type));
            // $('#jsCouponCode').attr('data-max-value', parseInt(coupon.disc_value_type));
            var discount_fix_value = 0;
            if($.trim($('#jsCouponCode').val()) != '') {
                var detail_discount = $('#jsCouponCode').attr('data-coupon');
                if($('#jsCouponCode').attr('data-value-type') == '1') {
                    discount_fix_value = parseInt($('#jsCouponCode').attr('data-value')) / 100 * grandTotal;
                    detail_discount += ' <i>(Potongan ' + parseInt($('#jsCouponCode').attr('data-value')) + ' %';
                    if($('#jsCouponCode').attr('data-max-value') && parseInt($('#jsCouponCode').attr('data-max-value')) > 0 ) {
                        detail_discount += ' maksimal ' + maskMoney($('#jsCouponCode').attr('data-max-value'));
                        if(parseInt($('#jsCouponCode').attr('data-max-value')) < discount_fix_value) {
                            discount_fix_value = $('#jsCouponCode').attr('data-max-value');
                        }
                    }
                    detail_discount += ')</i>';
                }
                else {
                    discount_fix_value = parseInt($('#jsCouponCode').attr('data-value'));
                    if(discount_fix_value>grandTotal) {
                        discount_fix_value = grandTotal;
                    }
                    detail_discount += ' <i>(Potongan ' + parseInt($('#jsCouponCode').attr('data-value')) + ')</i>';
                }
                $('#jsTempRowItemBody').append('<tr class="js-row-item-appended" style="color: red;"><td>&nbsp;</td><td>'+ detail_discount +'</td><td>&nbsp;</td><td id="jsDiscountValue" style="text-align: right;">- '+maskMoney(discount_fix_value)+'</td></tr>');
            }

            grandTotal = maskMoney(grandTotal-discount_fix_value);
            $('#jsTempRowItemBody').append('<tr class="js-row-item-appended" style="color: green;"><td colspan="2">&nbsp;</td><td>Grand Total</td><td id="jsGrandTotal" style="text-align: right;">'+grandTotal+'</td></tr>');
        }
    }
    //action create trans for guest
    $('#jsBtnCreateGuestTrans').on('click', function(e){
        e.preventDefault();
        if($('#jsGuestName').val() == '') {
            alert('Isi Nama Guest ...')
        }
        else {
            $form = $('#jsFormCreateGuestTrans');
            $.ajax({
        		url:$form.attr('action'),
        		method:"POST",
        		dataType:'JSON',
          	    async: false,
        		data:$form.serializeArray(),
        		success:function(response){
                    if(response.data) {
                        var value = response.data;
                        $row = '<li class="js-ongoing-trans-appended list-group-item" style="display: none;text-align: left;"><a class="js-view-ongoing-trans" data-trans-id="'+value.id +'" href="#" style="display: block;">'+ value.customer_name+' #' + value.id +'</a> | <a class="js-remove-ongoing-trans" data-trans-id="'+value.id +'" href="#"><span class="badge badge-danger" href="#">Hapus Transaksi</span></a></li>';
                        $('#jsListOngoingTrans').append($row);
                        setTimeout(function(){
                            $('#jsLoadingOngoingTrans').hide();
                            $('#jsNoDataOnGoingTrans').hide();
                            hideAllModals();
                            $('.js-ongoing-trans-appended').show();
                        }, 1000);
                    }
                    else {
                        showErrorMessage('Gagal membuat transaksi <a href="#" class="js-hide-all-bootbox">[Tutup]</a>');
                    }
                },
                error: function (request, status, error) {
                    showErrorMessageByResponseCode(request.status);
                },
                beforeSend: function() {
                    console.log('create-guest-transaction-start');
                    hideAllModals();
                    loadingWithMessage('Sedang membuat transaksi . . .');
                },
                complete: function() {
                    console.log('create-guest-transaction-finish');
                    hideAllModals();
                }
            });
        }

    });

    $('#jsRefreshOnGoingTransList').trigger('click');

    $('#jsBtnResetSelectedItem').click(function(e){
        $('#jsSearchItemKeyword').prop('disabled', false);
        $('#jsSearchItemKeyword').val('');
        $('#jsSearchItemKeyword').attr('title', '');
        $('#jsSelectedItemCode').val('');
        $('#jsQty').attr('data-price', 0);
        $('#jsQty').val(1);
        $('#jsQty').trigger('change');
        $('#jsAddItemOngoingTrans').prop('disabled', true);
        $('#jsBtnResetSelectedItem').prop('disabled', true);
    });

    $(document).on('click', '.js-remove-item', function(e){
        e.preventDefault();
        $this = $(this);
        $.ajax({
    		url: $this.attr('href'),
    		method:"POST",
    		dataType:'JSON',
      	    async: false,
    		success:function(response){
                if(response.data) {
                    $this.parents('tr').remove();
                }
                else if(response.message) {
                    showErrorMessage(response.message, true);
                }
                else {
                    setTimeout(function(){
                        hideAllModals();
                    }, 1000);
                }
                calculateGrandTotal();
            },
            error: function (request, status, error) {
                showErrorMessageByResponseCode(request.status);
            },
        });
    });

    $('#jsAddSeriesItem').click(function(e){
        e.preventDefault();
        $('#jsTransToSeries').html($('#jsModalTitleOngoingTrans').text());

        hideAllModals();
        $('#jsModalSeriesTransConfirmation').modal('show');
    });

    $('#jsAddItemOngoingTrans').click(function(e){
        e.preventDefault();
        $is_existing = $('.js-item-number[value='+$('#jsSelectedItemCode').val()+']').length > 0;
        if(!$is_existing) {
            $form = $(this).parents('form');
            $.ajax({
        		url:$form.attr('action').replace('trans_id', $('#jsViewedOngoingTransId').val()),
        		method:"POST",
        		dataType:'JSON',
          	    async: false,
                data: $form.serializeArray(),
        		success:function(response){
                    if(response.data) {
                        $('#jsGrandTotal').parents('tr').remove();
                        appendItemRow(response.data);
                        calculateGrandTotal();
                        $('#jsBtnResetSelectedItem').trigger('click');
                    }
                    else {
                        setTimeout(function(){
                            hideAllModals();
                        }, 1000);
                    }
                },
                error: function (request, status, error) {
                    showErrorMessageByResponseCode(request.status);
                },
            });
        }
        else {
            toastr["warning"]("Item sudah pernah ditambahkan, silahkan ubah Quantity", "Perhatikan");
            $('#jsBtnResetSelectedItem').trigger('click');
        }

    });

    $('#jsApplyCoupon').click(function(e){
        e.preventDefault();
        var ajax_flag = true;
        if($.trim($(this).html()) == 'Hapus') {
            $('#jsCouponCode').val('');
        }
        else if($.trim($('#jsCouponCode').val()) == '') {
            toastr["warning"]("Isi kode kupon terlebih dahulu", "Perhatikan");
            ajax_flag = false;
        }
        if(ajax_flag) {
            $.ajax({
                url:$('#jsUrlApplyCoupon').val().replace('trans_id', $('#jsViewedOngoingTransId').val()),
                method:"POST",
                dataType:'JSON',
                async: false,
                data: {
                    code: $('#jsCouponCode').val()
                },
                success:function(response){
                    if(response.data) {
                        var coupon = response.data;
                        $('#jsCouponCode').val(coupon.coupon_code);
                        $('#jsCouponCode').prop('disabled', true);
                        $('#jsCouponCode').attr('data-coupon', 'Ref#'+coupon.id + ' <b>' + coupon.coupon_code + '</b>');
                        $('#jsCouponCode').attr('data-value', parseInt(coupon.disc_value));
                        $('#jsCouponCode').attr('data-value-type', parseInt(coupon.disc_value_type));
                        $('#jsCouponCode').attr('data-max-value', parseInt(coupon.max_fix_value));
                        $('#jsApplyCoupon').addClass('btn-danger');
                        $('#jsApplyCoupon').html('Hapus');
                        toastr["success"]("Kupon berhasil diterapkan pada transaksi ini", "Berhasil");
                    }
                    else {
                        $('#jsCouponCode').val('');
                        $('#jsCouponCode').prop('disabled', false);
                        $('#jsCouponCode').removeAttr('data-coupon');
                        $('#jsCouponCode').removeAttr('data-value');
                        $('#jsCouponCode').removeAttr('data-value-type');
                        $('#jsCouponCode').removeAttr('data-max-value');
                        $('#jsApplyCoupon').removeClass('btn-danger');
                        $('#jsApplyCoupon').html('Apply');
                        if(response.message) {
                            toastr["error"](response.message, "Gagal");
                        }
                    }
                    calculateGrandTotal();
                },
                error: function (request, status, error) {
                    showErrorMessageByResponseCode(request.status);
                },
            });
        }
    });

    $(document).on('click', '.js-remove-ongoing-trans', function(e){
        e.preventDefault();
        var trans_to_remove = $('a[data-trans-id="'+$(this).attr('data-trans-id')+'"]').html();
        console.log('remove-ongoing-transaction-'+trans_to_remove);

        $('#jsTransIdToRemove').val($(this).attr('data-trans-id'));
        $('#jsTransToRemove').html(trans_to_remove);
        $('#jsModalRemoveOngoingTransConfirmation').modal('show');
    });

    $(document).on('click', '#jsRemoveThisTrans', function(e){
        e.preventDefault();
        if($('#jsTransIdToRemove').val() != '') {
            hideAllModals();
            loadingWithMessage('Sedang menghapus transaksi '+$('#jsTransToRemove').text());
            $.ajax({
                url: $('#jsUrlRemoveOngoingTransData').val()+'/'+$('#jsTransIdToRemove').val(),
                method:"POST",
                dataType:'JSON',
                async: false,
                success:function(response){
                    setTimeout(function(){
                        hideAllModals();
                        toastr["success"]("transaksi "+$('#jsTransToRemove').text() +" berhasil dihapus", "Berhasil");
                        $('#jsTransToRemove').html('');
                        $('#jsTransIdToRemove').val('');
                        hideAllModals();
                        $('#jsRefreshOnGoingTransList').trigger('click');
                    }, 1000);

                },
                error: function (request, status, error) {
                    showErrorMessageByResponseCode(request.status);
                },
            });
        }
        else {
            showErrorMessageByResponseCode('--');
        }


    });



    $(document).on('click', '.js-view-ongoing-trans', function(e){
        e.preventDefault();
        console.log('view-ongoing-transaction-'+$(this).attr('data-trans-id'));
        $('#jsViewedOngoingTransId').val($(this).attr('data-trans-id'));
        $('#jsModalTitleOngoingTrans').html('. . .');
        $('#jsViewOngoingTransData').hide();
        $('#jsViewOngoingTransLoading').show();
        $('.js-row-item-appended').remove();
        $('#jsSearchItemKeyword').prop('disabled', false);
        $('#jsSearchItemKeyword').val('');
        $('#jsSearchItemKeyword').attr('title', '');
        $('#jsQty').attr('data-price', 0);
        $('#jsQty').val(1);
        $('#jsQty').trigger('change');
        $('#jsBtnResetSelectedItem').prop('disabled', true);
        $('#jsAddItemOngoingTrans').prop('disabled', true);
        $('#jsModalViewOngoingTrans').modal('show');
        $.ajax({
    		url: $('#jsUrlGetOngoingTransData').val()+'/'+$(this).attr('data-trans-id'),
    		method:"POST",
    		dataType:'JSON',
      	    async: false,
    		success:function(response){
                if(response.data) {
                    if(response.data.is_series) {
                        // $('#jsModalViewOngoingTrans').modal('hide')
                        // loadingWithMessage('Menuju halaman Paket Series . . .');
                        window.location.href = $('#jsUrlPaketSeriesTrans').val().replace('trans_id', $('#jsViewedOngoingTransId').val());
                    }
                    else {
                        $('#jsModalTitleOngoingTrans').html(response.data.header_data.member_id + ' - ' + response.data.header_data.customer_name + ' #' + response.data.header_data.trans_id);
                        $('#jsViewOngoingTransLoading').hide();
                        $('#jsViewOngoingTransData').show();

                        if(response.data.detail_data) {
                            $.each( response.data.detail_data, function( key, value ) {
                                appendItemRow(value);
                            });
                        }
                        else {
                            console.log('no-item-exists');
                        }

                        if(response.data.coupon_data) {
                            var coupon = response.data.coupon_data;
                            $('#jsCouponCode').val(coupon.coupon_code);
                            $('#jsCouponCode').prop('disabled', true);
                            $('#jsCouponCode').attr('data-coupon', 'Ref#'+coupon.id + ' <b>' + coupon.coupon_code + '</b>');
                            $('#jsCouponCode').attr('data-value', parseInt(coupon.disc_value));
                            $('#jsCouponCode').attr('data-value-type', parseInt(coupon.disc_value_type));
                            $('#jsCouponCode').attr('data-max-value', parseInt(coupon.max_fix_value));
                            $('#jsApplyCoupon').addClass('btn-danger');
                            $('#jsApplyCoupon').html('Hapus');
                        }
                        else {
                            $('#jsCouponCode').val('');
                            $('#jsCouponCode').prop('disabled', false);
                            $('#jsCouponCode').removeAttr('data-coupon');
                            $('#jsCouponCode').removeAttr('data-value');
                            $('#jsCouponCode').removeAttr('data-value-type');
                            $('#jsCouponCode').removeAttr('data-max-value');
                            $('#jsApplyCoupon').removeClass('btn-danger');
                            $('#jsApplyCoupon').html('Apply');
                        }
                        calculateGrandTotal();
                    }

                }
                else {
                    setTimeout(function(){
                        hideAllModals();
                    }, 1000);
                }
            },
            error: function (request, status, error) {
                showErrorMessageByResponseCode(request.status);
            },
        });
    });

    $('#jsQty').change(function(e){
            console.log('calculate-sub-total');
            $('#jsSubTotalSelectedItem').html(maskMoney(parseInt($(this).attr('data-price'))*parseInt($(this).val())));
    });

    $('.js-item-qty').change(function(e){
            console.log('re-calculate-sub-total');
            var new_sub_total = parseInt($(this).attr('data-price'))*parseInt($(this).val());
            $(this).parents('tr').find('.js-item-sub-total-price').attr('data-sub-total-price', new_sub_total);
            $(this).parents('tr').find('.js-item-sub-total-price').html(maskMoney(new_sub_total));
            calculateGrandTotal();
            $.ajax({
        		url: $('#jsUrlUpdateItem').val().replace('trans_id', $('#jsViewedOngoingTransId').val()),
        		method:"POST",
        		dataType:'JSON',
          	    async: false,
                data: {
                    item_number: $(this).parents('td').find('.js-item-number').val(),
                    item_qty: $(this).val()
                },
        		success:function(response){

                },
                error: function (request, status, error) {
                    showErrorMessageByResponseCode(request.status);
                },
            });
    });

    setInterval(function(){
        $('#jsRefreshOnGoingTransList').trigger('click');
    }, 120000);
});
</script>
@endsection
