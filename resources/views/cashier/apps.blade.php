<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Amanie - </title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- <link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" /> -->
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->

        <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="../css/custom.css" rel="stylesheet" type="text/css" />

    </head>
    <body class="container">

        <div class="page-container col-md-12">
            <div class="row">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="fa fa-user font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase">Kasir: {{Sentinel::getUser()->first_name}} | Cabang: {{$branch->branch_name}}</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="col-md-6">
                            <div class="ui-widget">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio">
                                        <input type="radio" name="member_type" id="member_type1" value="member" checked="">
                                         Member
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="member_type" id="member_type2" value="umum">
                                         Umum
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="ui-widget">
                                <input id="member" type="text" class="form-control" placeholder="* Nama / Id Member"/>
                                <div id="form_add_item" style="border: 1px black solid; padding: 20px; margin-top: 10px;">
                                    <input id="items" type="text" class="form-control" placeholder="* Nama / Id Item"/>
                                    <input id="item_selected" type="hidden" value="" />
                                    <input id="item_selected_price" type="hidden" value="" />
                                    <input id="item_pic" type="text" class="form-control" placeholder="* Nama / Id PIC" style="margin-top: 5px; display: none;"/>
                                    <input id="pic_selected" type="hidden" value="" />
                                    <input class="datepicker form-control" id="date_to_rent"  placeholder="* Tanggal sewa" style="display:none; margin-top: 5px;">
                                    <input class="form-control" id="branch_to_rent"  placeholder="* Tempat pengambilan" style="display:none; margin-top: 5px;">
                                    <input id="branch_to_rent_selected" type="hidden" value="" />
                                    <input id="item_qty" type="text" class="form-control" placeholder="* Banyaknya" style="margin-top: 5px;"/>
                                    <input id="item_discount" type="text" class="form-control" placeholder="Diskon item" style="margin-top: 5px;"/>
                                    <label class="bold">Tipe Diskon</label>
                                    <label class="mt-radio">
                                        <input type="radio" name="discount_type" id="discount_type1" value="persen" checked="">
                                         Persen
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="discount_type" id="discount_type2" value="pasti">
                                         Nilai Pasti
                                        <span></span>
                                    </label>
                                    <div class="form-action" style="margin-top: 5px;">
                                        <div id="item_price_total" style="text-align: right">0</div>
                                        <!-- <div id="discount_total" style="text-align: right">0</div> -->
                                        <div class="general-error"></div>
                                        <a id="btn_add" class="btn purple-rev">Tambah</a>
                                        <a id="btn_reset" class="btn default">Reset</a>
                                    </div>
                                    <div class="form-action text-right" style="margin-top: 5px;">
                                        <a id="btn_final" class="btn default">SELANJUTNYA ></a>
                                    </div>
                                </div>
                            </div>
                            <div class="ui-widget">
                                <div id="form_final" style="border: 1px black solid; padding: 20px; margin-top: 10px; display: none;">
                                    <div class="form-action text-right" style="margin-top: 5px;">
                                        <a id="btn_add_item" class="btn default">< SEBELUMNYA</a>
                                    </div>
                                    <input id="discount_total" type="text" class="form-control" placeholder="Diskon dari Total" style="margin-top: 5px;"/>
                                    <label class="bold">Tipe Diskon</label>
                                    <label class="mt-radio">
                                        <input type="radio" name="discount_total_type" id="discount_total_type1" value="persen" checked="">
                                         Persen
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="discount_total_type" id="discount_total_type2" value="pasti">
                                         Nilai Pasti
                                        <span></span>
                                    </label>
                                    <input id="others" type="text" class="form-control" placeholder="Biaya Lain-lain" style="margin-top: 5px;"/>
                                    <hr/>
                                    <select id="payment_type" class="form-control" name="branch" style="margin-top: 5px;">
                                        <option value="">* Pilih Tipe Pembayaran</option>
                                            <option value="1">Tunai</option>
                                            <option value="2">Kredit</option>
                                            <option value="3">Credit Card</option>
                                            <option value="4">Debit Card</option>
                                        </option>
                                    </select>
                                    <input id="total_paid" type="text" class="form-control" placeholder="*Jumlah Bayar" style="margin-top: 5px; display: none;"/>
                                    <div class="form-action" style="margin-top: 5px;">
                                        <div class="general-error"></div>
                                        <a id="btn_process" class="btn purple-rev">Proses</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="transaction" class="col-md-6">
                            {!! Form::open(['route' => 'do.transaction']) !!}
                            <!-- <div class="text-right bold">
                                INV/KEP/14042017/00001
                            </div>
                            <div class="text-right bold">
                                <label id="lbl_name">-</label>
                                <span class="fa fa-user"></span>
                                <input id="member_selected" type="hidden" value=""/>
                            </div> -->
                            <div id="detail_header" class="bold uppercase">
                                <div class="col-md-12 row_trans" style="border-bottom: 1px black solid;">
                                    <div class="col-md-8">
                                        Item
                                    </div>
                                    <div class="col-md-1">
                                        Qty
                                    </div>
                                    <div class="col-md-3" style="text-align: right;">
                                        Total
                                    </div>

                                </div>
                            </div>
                            <div id="detail_transaction">
                            </div>

                            <div id="footer_transaction">
                                <div class="col-md-12" style="border-top: 1px black solid;">
                                    <div class="col-md-9" style="text-align: right;">
                                        Total
                                    </div>
                                    <div id="footer_total" class="col-md-3" style="text-align: right;">
                                        0
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-9" style="text-align: right;">
                                        Potongan
                                    </div>
                                    <div id="footer_discount" class="col-md-3" style="text-align: right;">
                                        0
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-9" style="text-align: right;">
                                        Biaya lain-lain
                                    </div>
                                    <div id="footer_others" class="col-md-3" style="text-align: right;">
                                        0
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-9" style="text-align: right;">
                                        Total Akhir
                                    </div>
                                    <div id="footer_end_total" class="col-md-3" style="text-align: right;">
                                        0
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr/>
                                    <div class="col-md-9" style="text-align: right;">
                                        Bayar:
                                    </div>
                                    <div id="footer_total_paid" class="col-md-3" style="text-align: right;">
                                        0
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-9" style="text-align: right;">
                                        Kembalian:
                                    </div>
                                    <div id="footer_change" class="col-md-3" style="text-align: right;">
                                        0
                                    </div>
                                </div>
                                <input type="hidden" value="0" id="total_temp" name="total_temp" />
                                <input type="hidden" value="0" id="discount_temp" name="discount_temp" />
                                <input type="hidden" value="0" id="end_total_temp" name="end_total_temp" />
                                <input type="hidden" value="0" id="discount_total_temp" name="discount_total_temp" />
                                <input type="hidden" value="0" id="discount_total_type_temp" name="discount_total_type_temp" />
                                <input type="hidden" value="0" id="discount_total_fixed_temp" name="discount_total_fixed_temp" />
                                <input type="hidden" value="0" id="others_temp" name="others_temp" />
                                <input type="hidden" value="0" id="total_paid_temp" name="total_paid_temp" />
                                <input type="hidden" value="" id="member_temp" name="member_temp" />
                                <input type="hidden" value="1" id="payment_type_temp" name="payment_type_temp" />
                                @if($employee_data==null)
                                <input type="hidden" value="{{Crypt::encryptString($branch->id)}}" id="cashier_branch_temp" name="cashier_branch_temp" />
                                @endif
                            </div>
                            <button id="btn_process_submit" class='submit-button' type="submit" style="display:none;">TEST</button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="temp_detail" class="col-md-12 row_trans" style="display: none;">
                <div class="col-md-1">
                    <a class="btn_remove" data-item-id="" data-item-total-price="0" data-item-discount="0">[X]</a>
                </div>
                <div class="col-md-7 item_name">
                    ITEM NAMEEEEEE
                </div>
                <div class="col-md-1 item_qty">
                    5
                </div>
                <div class="col-md-3 item_total_price" style="text-align: right;">
                    2000000
                </div>
            </div>
            <input type="hidden" id="temp_input" class="" value="" name="list_inputs[]" />
            <input type="hidden" id="get_pic" value="{{route('get.pic.cashier')}}" />
            <input type="hidden" id="get_members" value="{{route('get.members.cashier')}}" />
            <input type="hidden" id="get_items" value="{{route('get.items.cashier',['branch'=>Crypt::encryptString($branch->id)])}}/" />
            <input type="hidden" id="get_branches" value="{{route('get.branches.cashier')}}/" />
        </div>
        <!--[if lt IE 9]>
        <script src="../assets/global/plugins/respond.min.js"></script>
        <script src="../assets/global/plugins/excanvas.min.js"></script>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="../js/jquery.js" type="text/javascript"></script>
        <script src="../js/jquery-ui.js" type="text/javascript"></script>
        <script src="../js/amanie.js" type="text/javascript"></script>
        <script src="../js/cashier.js" type="text/javascript"></script>
        <script src="https://printjs-4de6.kxcdn.com/print.min.js" type="text/javascript"></script>

        <!-- <script src="../assets/global/plugins/moment.min.js" type="text/javascript"></script> -->
        <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->

        <script src="../assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
        <script src="../assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>
