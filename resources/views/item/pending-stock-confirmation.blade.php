@extends('master')
<?php /*
@section('optional_css')
@endsection
*/ ?>
@section('optional_js')
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('.modal-confirmation').click(function(){
            $('#modal_message').html($(this).attr('data-message'));
            if($(this).attr('data-confirm-type') =='2') {
                $('#btn_confirmation').html('Reject');
                $('#btn_confirmation').removeClass('btn-primary').addClass('btn-danger');
            }
            else if($(this).attr('data-confirm-type') =='3') {
                $('#btn_confirmation').html('Approve');
                $('#btn_confirmation').removeClass('btn-danger').addClass('btn-primary');
            }
            $('#confirmation_type').val($(this).attr('data-confirm-type'));
            $('#confirmation_id').val($(this).attr('data-pending-id'));
            $('#modal_confirmation').modal('show');
        })
        Bootbox.init();
    });
</script>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list-ol font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Konfirmasi Stok Masuk Pending</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-info">
                    @include('item.confirmation-supply-branch-menu')
                </div>
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col" style="width:100px !important">Kategori</th>
                                <th scope="col" style="width:150px !important">Jumlah Stok Masuk</th>
                                <th scope="col" style="width:150px !important">Oleh</th>
                                <th scope="col">Note</th>
                                <th scope="col" style="width:200px !important">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending_confirmations as $pending_confirmation)
                                <tr>
                                    <td>{{ $pending_confirmation->item_id.' '.$pending_confirmation->item->item_name }}</td>
                                    <td>{{ ucwords(HelperService::itemTypeById($pending_confirmation->item->item_type)) }}</td>
                                    <td>{{ $pending_confirmation->stock}}</td>
                                    <td>{{ $pending_confirmation->user->first_name }}</td>
                                    <td>
                                    @if(empty($pending_confirmation->sender_note))
                                        -
                                    @else
                                        <a class="bootbox-view-note" data-message="{{$pending_confirmation->sender_note}}">
                                            <span class="fa fa-eye"></span> Lihat
                                        </a>
                                    @endif
                                    </td>
                                    <td class="text-center">
                                        <a data-pending-id="{{$pending_confirmation->id}}" data-confirm-type="3" class="modal-confirmation" data-message="Anda yakin ingin konfirmasi 'Approve' pada
                                        supply {{ $pending_confirmation->item_id.' '.$pending_confirmation->item->item_name }}
                                        oleh {{ $pending_confirmation->user->first_name }}
                                        sebanyak {{ $pending_confirmation->stock}}?">
                                            Approve
                                        </a>
                                         | <a  data-pending-id="{{$pending_confirmation->id}}" data-confirm-type="2" class="modal-confirmation" data-message="Anda yakin ingin konfirmasi 'Reject' pada
                                         supply {{ $pending_confirmation->item_id.' '.$pending_confirmation->item->item_name }}
                                         oleh {{ $pending_confirmation->user->first_name }}
                                         sebanyak {{ $pending_confirmation->stock}}?">
                                             Reject
                                         </a>
                                    </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modal_confirmation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    {!! Form::open(['id' => 'form_confirmation', 'route' => 'confirm.pending.confirmation','class'=>'form-horizontal']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modal_confirmation_title">Konfirmasi</h4>
        </div>
        <div class="modal-body">
            <div id="modal_message" class="bold">

            </div>
            <div class="bold"><hr/>Note</div>
            <textarea class="form-control" rows="3" name="note"></textarea>
            <input type="hidden" id="confirmation_id" name="confirmation_id" value="" />
            <input type="hidden" id="confirmation_type" name="confirmation_type" value="" />
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <a data-form-id="#form_confirmation" id="btn_confirmation" class="submit-button-validation btn btn-primary"></a>
        </div>
    {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection
