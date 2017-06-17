@extends('master')

<?php /*
@section('optional_css')
@endsection
*/ ?>

@section('optional_js')
<script src="../assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../js/configure-jasa.js" type="text/javascript"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gear font-purple-rev"></i>
                    <span class="caption-subject font-purple-rev bold">Konfigurasi Paket: {{ $item_paket->item_name}}</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Jasa</th>
                                <th scope="col" style="width:250px !important">Sebanyak</th>
                                <th scope="col" style="width:250px !important">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{ Form::open(['id' => 'form-add-configuration', 'class' => 'form-configure-jasa', 'route' => ['update.configuration.item.paket', Crypt::encryptString('add_new_configuration.'.request()->item_id)]]) }}
                                    <td>
                                        <select class="add-configuration-required form-control" name="item_jasa">
                                            <option value="">Pilih Jasa</option>
                                            @foreach($items_paket as $item_paket)
                                                <option value="{{Crypt::encryptString($item_paket->item_id)}}">{{$item_paket->item_name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="col-md-6">
                                            <input name="qty_jasa" type="text" value="" class="add-configuration-required form-control" />
                                        </div>
                                    </td>
                                    <td>
                                        <button type="submit" data-form-id="#form-add-configuration" data-unique=".add-configuration-required" class="btn green submit-button-validation">Tambah</button>
                                    </td>
                                {{ Form::close() }}
                            </tr>
                            @foreach($paket_configurations as $configuration)
                                {{ Form::open(['id' => 'form-'.$configuration->id, 'class' => 'form-configure-paket', 'route' => ['update.configuration.item.paket', Crypt::encryptString($configuration->id)]]) }}
                                <tr>
                                    <td>{{ $configuration->jasa->item_name }}</td>
                                    <td>
                                        <div class="col-md-6">
                                            <input name="qty_jasa" type="text" value="{{$configuration->qty_jasa}}" class="{{'form-'.$configuration->id.'-required'}} input-only-number form-control" />
                                        </div>
                                    </td>
                                    <td>
                                        <button type="submit" data-form-id="{{'#form-'.$configuration->id}}" data-unique="{{'.form-'.$configuration->id.'-required'}}" class="btn purple-rev submit-button-validation">Simpan</button>
                                        <a href="{{route('delete.configuration.item.paket',['id_encrypted'=>Crypt::encryptString($configuration->id)])}}" class="btn default">Hapus</a>
                                    </td>
                                {{ Form::close() }}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
