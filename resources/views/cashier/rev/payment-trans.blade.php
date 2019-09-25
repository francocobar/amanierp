@extends('master.master-pos')

@section('content')
    <div class="container marketing">
        <div class="row">
            <div class="col-lg-12 right">
                <a href="{{route('cashier.index', request()->branch_id)}}"><button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali ke Beranda Kasir</button></a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 justify-content-center">
                <div style="margin: 60px">
                    @if($trx_header->member_id)
                    <h3>Transaksi  <u>{{$trx_header->member_id.' - '.$trx_header->customer_name.' #'.$trx_header->id}}</u></h3>
                    @else
                    <h3>Transaksi  <u>{{$trx_header->customer_name.' #'.$trx_header->id}}</u></h3>
                    @endif
                </div>
                <div style="margin: 60px; text-align: left">
                    Total Tagihan: <b>Rp{{HelperService::maskMoney($trx_header->total)}}</b><br/>
                    Sudah dibayarkan: <b>Rp{{HelperService::maskMoney($already_paid)}}</b><br/>
                    Riwayat pembayaran:
                    @foreach($payments as $key => $payment)

                    @endforeach
                </div>
            </div><!-- /.col-lg-4 -->
            <div class="col-lg-6">
                <h2>Bayar secara</h2>
                <button disabled id="jsBtnFullPayment" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block" data-toggle="modal" data-target="#jsModalFullPayment">Penuh / Lunas</button>
                <button disabled id="jsBtnDownPayment" type="button" class="btn btn-amanie-opt btn-primary btn-lg btn-block" data-toggle="modal" data-target="#jsModalDownPayment">Uang Muka</button>

            </div>
        </div>
</div>
@endsection

@section('blade-hidden')
<input type="hidden" id="jsUrlGetOngoingTransData" value="{{route('cashier.get-ongoing-transaction', request()->trans_id)}}" />
<div class="modal fade" id="jsModalFullPayment" role="dialog" aria-labelledby="jsModalFullPayment" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsModalTitleFullPayment">Bayar Penuh: <span class="font-weight-bold"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        Metode Pembayaran:
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                          <label class="btn btn-secondary active">
                              <input value="1" type="radio" name="payment_method" id="option1" autocomplete="off" checked> Uang Cash
                          </label>
                          <label class="btn btn-secondary">
                              <input value="2" type="radio" name="payment_method" id="option2" autocomplete="off"> Kartu Debit
                          </label>
                          <label class="btn btn-secondary">
                              <input value="3" type="radio" name="payment_method" id="option3" autocomplete="off"> Kartu Kredit
                          </label>
                        </div>
                    </div>
                </div>
                <div class="row js-card-number" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsCardNumber" type="text" class="form-control" placeholder="Nomor pada Kartu"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsAmountToCashier" type="text" class="form-control js-mask-idr js-default-empty" placeholder="* Uang yang diserahkan ke Kasir"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        Kembalian: <b>Rp<span id="jsPaymentChange">0</span></b>
                    </div>
                </div>
                <div id="jsFinishPayment" class="row js-card-number" style="margin-top: 6px; display: hidden;">
                    <div class="col-md-12 text-right">
                        <button id="jsBtnFinishPayment" type="button" class="btn btn-primary">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="jsModalDownPayment" role="dialog" aria-labelledby="jsModalDownPayment" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jsModalTitleDownPayment">Bayar Uang Muka Ke-1</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input id="jsAmountDP" type="text" class="form-control js-mask-idr js-default-empty" placeholder="* Uang muka yang ingin dibayarkan"/>
                    </div>
                </div>
                <div class="row" style="margin-top: 6px">
                    <div class="col-md-12">
                        Metode Pembayaran:
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                          <label class="btn btn-secondary active">
                              <input value="1" type="radio" name="payment_method" id="option4" autocomplete="off" checked> Uang Cash
                          </label>
                          <label class="btn btn-secondary">
                              <input value="2" type="radio" name="payment_method" id="option5" autocomplete="off"> Kartu Debit
                          </label>
                          <label class="btn btn-secondary">
                              <input value="3" type="radio" name="payment_method" id="option6" autocomplete="off"> Kartu Kredit
                          </label>
                        </div>
                    </div>
                </div>
                <div class="row js-card-number" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsCardNumber2" type="text" class="form-control" placeholder="Nomor pada Kartu"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        <input id="jsAmountToCashier2" type="text" class="form-control js-mask-idr js-default-empty" placeholder="* Uang yang diserahkan ke Kasir"/>
                    </div>
                </div>
                <div class="row js-cash" style="margin-top: 6px">
                    <div class="col-md-12">
                        Kembalian: <b>Rp<span id="jsPaymentChange2">0</span></b>
                    </div>
                </div>
                <div id="jsFinishPayment2" class="row js-card-number" style="margin-top: 6px; display: hidden;">
                    <div class="col-md-12 text-right">
                        <button id="jsBtnFinishPayment2" type="button" class="btn btn-primary">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Form::open(['id' => 'jsFormPayOngoingTransaction', 'route' => array('cashier.pays-ongoing-transaction',request()->branch_id,request()->trans_id)]) !!}
<input id="jsPaymentMethod" type="hidden" name="payment_method" value="" />
<input id="jsValAmountToPay" type="hidden" name="amount_to_pay" value="" />
<input id="jsValAmountToCashier" type="hidden" name="amount_to_cashier" value="" />
<input id="jsValCardNumber" type="hidden" name="card_number" value="" />
{!! Form::close() !!}
@endsection

@section('blade-script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

@endsection

@section('blade-style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
@endsection
