@extends('master')
<?php /*
@section('optional_css')
@endsection
*/ ?>
@section('optional_js')
<script type="text/javascript">
$('.change_status').click(function(e){
    e.preventDefault();
    $member_id = $(this).closest('tr').find('.member_id').html();
    $member_name = $(this).closest('tr').find('.member_name').html();

    bootbox.prompt({
        title: '<b>Sebutkan alasan penghapusan <u>'+ $member_id + ' ' + $member_name +'</u>?</b>',
        inputType: 'textarea',
        buttons: {
            confirm: {
                label: 'Hapus',
                className: 'btn-success',
            },
            cancel: {
                label: 'Batal',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result == null) {
                $('#log').val('');
                $('#member_id').val('');
                return true;
            }
            else {
                if(result)
                {
                    $('#log').val(result);
                    $('#member_id').val($member_id);
                    validateForm($('#remove_member'));
                }
                else {
                    alert('Wajib menyertakan alasan penghapusan member');
                    return false;
                }

            }
        }
    });
});
</script>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-users font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold uppercase">Daftar Member</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row" style="margin-bottom: 5px;">
                    <div class="col-md-offset-3 col-md-6">
                        <form class="" action="" method="get">
                            <div class="form-body">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="name" placeholder="Masukkan Member Id atau Nama" value="{{ request()->name }}" / >
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn purple-rev">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="alert alert-info">
                    {!! $message.$keyword !!}
                    {!! HelperService::generatePaging(request()->page, $total_page) !!}
                </div>

                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="width:450px !important">Id Member</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Cabang</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td class="member_id">{{$member->member_id}}
                                    </td>
                                    <td class="member_name">{{$member->full_name}}</td>
                                    <td>{{$member->branch->branch_name}}</td>
                                    <td><a class="btn btn-success" href="{{$member->edit_member_id_url()}}">Edit Data</a>
                                        <a class="change_status btn btn-danger">Hapus</a>
                                    </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Form::open(['route' => 'remove.member.do', 'id'=>'remove_member','style'=>'diplay: none;'])!!}
    <input type="hidden" id="log" name="log" />
    <input type="hidden" id="member_id" name="member_id" />
{!! Form::close() !!}
@endsection
