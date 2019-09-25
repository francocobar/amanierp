@extends('master.master-pos')

@section('content')
    <div class="container marketing">

        <!-- Three columns of text below the carousel -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <button id="jsCreateCoupon" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block" data-toggle="modal" data-target="#jsModalCreateCoupon">Buat Kupon Promo</button>
                <button id="jsGetCoupons" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block">Kupon Promo Terdaftar</button>
                <button type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block">Info Kupon Promo</button>
            </div><!-- /.col-lg-4 -->
        </div><!-- /.row -->
</div>
@endsection

@section('blade-hidden')
<input type="hidden" id="jsGetCouponsUrl" value="{{route('promo.coupons')}}" />
<!-- Modal Create Coupon -->
<div class="modal fade" id="jsModalCreateCoupon" role="dialog" aria-labelledby="jsModalCreateCoupon" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['id' => 'jsFormCreateCoupon', 'route' => 'promo.creates-coupon']) !!}
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle2">Buat Kupon Promo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input autocomplete="off" id="jsPromoName" type="text" class="form-control js-default-empty" name="promo_name" placeholder="* Nama promo" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input autocomplete="off" id="jsPromoCode" type="text" class="form-control js-default-empty" name="promo_code" placeholder="* Kode kupon" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input autocomplete="off" id="jsValidDate" type="text" class="form-control js-default-empty" placeholder="* Tanggal berlaku" required/>
                        <input id="jsValidFrom" type="hidden" name="promo_valid_from" value="" />
                        <input id="jsValidTo" type="hidden" name="promo_valid_to" value="" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <div class="form-check">
                            <input name="is_percent" value="1" class="form-check-input  js-default-checked" type="checkbox" id="jsIsPercent" checked>
                            <label class="form-check-label" for="jsIsPercent">
                                Nilai potongan dalam persen
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input autocomplete="off" id="jsDiscPercentValue" type="text" class="form-control js-mask-percent js-default-empty js-default-display" placeholder="* Nilai potongan (dalam persen)" required/>
                        <input autocomplete="off" id="jsDiscFixValue" type="text" class="form-control js-mask-idr js-default-empty js-default-display-n js-default-required-n" placeholder="* Nilai potongan (dalam rupiah)" name="disc_fix_value" style="display: none;"/>
                        <input id="jsDiscPercentValue2" type="hidden" name="disc_percent_value" value="" />
                        <input id="jsDiscFixValue2" type="hidden" name="disc_fix_value" value="" />
                    </div>
                </div>
                <div class="row js-default-display">
                    <div class="col-md-12 form-group">
                        <div class="form-check">
                            <input name="has_max_fix_value" value="1" class="form-check-input js-default-display js-default-checked" type="checkbox" id="jshasMaxValue" checked>
                            <label class="form-check-label" for="jshasMaxValue">
                                Nilai potongan memiliki nilai maksimal (dalam rupiah)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input autocomplete="off" id="jsDiscMaxFixValue" type="text" class="form-control js-mask-idr js-default-empty js-default-display" placeholder="* Maksimal nilai potongan (dalam rupiah)" required/>
                        <input id="jsDiscMaxFixValue2" type="hidden" name="disc_max_fix_value" value="" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi promo"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                <button id="jsBtnCreateCoupon" type="submit" class="btn btn-primary">Buat Kupon</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Modal Kupon Promo Terdaftar -->
<div id="jsModalCoupons" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="jsModalCoupons" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 80% !important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle3">Kupon Promo Terdaftar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="jsTableCouponList" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode Kupon</th>
                            <th>Diskon</th>
                            <th>Maksimal</th>
                            <th>Berlaku Mulai</th>
                            <th>Berlaku Sampai</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody id="jsTbodyCouponList">
                        <!-- <tr>
                            <td>Tiger Nixon</td>
                            <td>System Architect</td>
                            <td>Edinburgh</td>
                            <td>61</td>
                            <td>2011/04/25</td>
                            <td>$320,800</td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                <button id="jsBtnCreateCoupon" type="submit" class="btn btn-primary">Buat Kupon</button>
            </div> -->
        </div>
    </div>
</div>
@endsection

@section('blade-script')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
var flag_set_default = true;
$(document).ready(function(){
    var url_get_coupons = $('#jsGetCouponsUrl').val();

    $('#jsFormCreateCoupon').submit(function(e) {
        e.preventDefault();
        // $(this).find('.js-mask-idr').maskMoney('destroy');
        // $(this).find('.js-mask-percent').maskMoney('destroy');
        $('#jsDiscPercentValue2').val('');
        $('#jsDiscFixValue2').val('');
        $('#jsDiscMaxFixValue2').val('');
        if($('#jsIsPercent').prop('checked')) {
            $('#jsDiscPercentValue2').val($('#jsDiscPercentValue').maskMoney('unmasked')[0] * 100);
            if($('#jshasMaxValue').prop('checked')) {
                $('#jsDiscMaxFixValue2').val($('#jsDiscMaxFixValue').maskMoney('unmasked')[0] * 1000);
            }
        }
        else {
            $('#jsDiscFixValue2').val($('#jsDiscFixValue').maskMoney('unmasked')[0] * 1000);
        }
        $form = $(this);
        $.ajax({
    		url:$form.attr('action'),
    		method:"POST",
    		dataType:'JSON',
      	    async: false,
    		data:$form.serializeArray(),
    		success:function(response){
                setTimeout(function(){
                    hideAllBootbox();
                }, 1000);
                if(response.status) {
                    toastr["success"]("Kupon berhasil dibuat", "Berhasil");
                }
                else {
                    flag_set_default = false;
                    if(response.message) {
                        toastr["error"](response.message, "Gagal");
                    }
                    else {
                        toastr["error"]("Gagal membuat kupon", "Gagal");
                    }
                    setTimeout(function(){
                        $('#jsModalCreateCoupon').modal('show');
                    }, 1500);

                    // showErrorMessage('Gagal membuat Kupon <a href="#" class="js-hide-all-bootbox">[Tutup]</a>');
                }
            },
            error: function (request, status, error) {
                showErrorMessageByResponseCode(request.status);
            },
            beforeSend: function() {
                console.log('create-coupon-start');
                hideAllModals();
                loadingWithMessage('Sedang membuat kupon promo . . .');
            },
            complete: function() {
                console.log('create-coupon-finish');
            }
        });
    });

    $('#jsModalCreateCoupon').on('show.bs.modal', function (e) {
        if(flag_set_default) {
            setFieldsDefaultValue('#jsFormCreateCoupon');
        }
        flag_set_default = true;
    });

    $('#jsValidDate').daterangepicker({
            timePicker: true,
            autoUpdateInput: false,
            // startDate: moment().startOf('day'),
            // endDate: moment().endOf('day').add(168, 'hour'),
            locale: {
                format: 'DD-MM-YYYY HH:mm'
            },
            "timePicker24Hour": true,
        }, function(start, end, label) {
                $('#jsValidDate').val(start.format('DD-MM-YYYY HH:mm') + ' sampai ' + end.format('DD-MM-YYYY HH:mm'));
                $('#jsValidFrom').val(start.format('YYYY-MM-DD HH:mm:ss'));
                $('#jsValidTo').val(end.format('YYYY-MM-DD HH:mm:ss'));
                console.log('New date range selected: ' + start.format('YYYY-MM-DD HH:mm:ss') + ' to ' + end.format('YYYY-MM-DD HH:mm:ss') + ' (predefined range: ' + label + ')');
        });

    $('#jsIsPercent').change(function() {
        $('#jsDiscPercentValue').val('');
        $('#jsDiscFixValue').val('');
        $('#jsDiscMaxFixValue').val('');
        $('#jshasMaxValue').show();
        if($(this).prop('checked')) {
            $('#jsDiscPercentValue').show();
            $('#jsDiscPercentValue').prop('required', true);
            $('#jshasMaxValue').show();
            $('#jshasMaxValue').prop('checked', true);
            $('#jshasMaxValue').parents('.row').show();
            $('#jsDiscMaxFixValue').show();
            $('#jsDiscMaxFixValue').prop('required', true);
            $('#jsDiscFixValue').hide();
            $('#jsDiscFixValue').prop('required', false);
            $('#jshasMaxValue').trigger('change');
        }
        else {
            $('#jsDiscPercentValue').hide();
            $('#jsDiscPercentValue').prop('required', false);
            $('#jshasMaxValue').hide();
            $('#jshasMaxValue').prop('checked', false);
            $('#jshasMaxValue').parents('.row').hide();
            $('#jsDiscMaxFixValue').hide();
            $('#jsDiscMaxFixValue').prop('required', false);
            $('#jsDiscFixValue').show();
            $('#jsDiscFixValue').prop('required', true);
        }
    });

    $('#jshasMaxValue').change(function() {
        $('#jsDiscMaxFixValue').val('');
        if($(this).prop('checked')) {
            $('#jsDiscMaxFixValue').show();
            $('#jsDiscMaxFixValue').prop('required', true);
        }
        else {
            $('#jsDiscMaxFixValue').hide();
            $('#jsDiscMaxFixValue').prop('required', false);
        }
    });

    $('#jsGetCoupons').click(function(e){
        e.preventDefault();

        $.ajax({
    		url:url_get_coupons,
    		method:"POST",
      	    async: false,
    		success:function(response){
                $('#jsTbodyCouponList').html(response);
                $('#jsModalCoupons').modal('show');
                $('#jsTableCouponList').DataTable();
            },
            error: function (request, status, error) {
                showErrorMessageByResponseCode(request.status);
            },
            beforeSend: function() {
                console.log('get-coupons-start');
            },
            complete: function() {
                console.log('get-coupons-finish');
            }
        });
    });

    setTimeout(function(){
        $('#jsGetCouponsUrl').remove();
    }, 1500);
});
</script>
@endsection

@section('blade-style')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
@endsection
