@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li>
                            <a href="{{ URL::to('/depot')}}">All Distributor</a>
                        </li>
                        <li class="active">
                            <strong>Edit Distributor</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>Success!</strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Distributor </h1>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="{{route('depot.update',$depot->id)}}" enctype="multipart/form-data"
                                  method="post">
                                {{csrf_field()}}
                                {{method_field('PUT')}}

                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Sales
                                            Group
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="slgp_id" id="slgp_id"
                                                    required>

                                                @foreach ($salesGroups as $salesGroup)
                                                    <option value="{{ $salesGroup->id }}">{{ $salesGroup->company()->acmp_name.'-'.$salesGroup->slgp_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thana
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="than_id" id="than_id"
                                                    required>
                                                <
                                                @foreach ($govGeos as $govGeo)
                                                    <option value="{{ $govGeo->id }}">{{ $govGeo->id.'-'.$govGeo->than_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Base
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="base_id" id="base_id"
                                                    required>


                                                @foreach ($salesGeos as $salesGeo)
                                                    <option value="{{ $salesGeo->id }}">{{ $salesGeo->id.'-'.$salesGeo->base_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_name" name="dlrm_name"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$depot->dlrm_name}}"
                                                   placeholder="Name" required="required" type="text">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Code
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_code" name="dlrm_code"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$depot->dlrm_code}}"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln
                                            Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_olnm" name="dlrm_olnm"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$depot->dlrm_olnm}}"
                                                   placeholder="Ln Name" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Address

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_adrs" name="dlrm_adrs"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$depot->dlrm_adrs}}"
                                                   placeholder="Address" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln
                                            Address
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_olad" name="dlrm_olad"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$depot->dlrm_olad}}"
                                                   placeholder="Ln Address" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Owner
                                            Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_ownm" name="dlrm_ownm"
                                                   class="form-control col-md-7 col-xs-12"
                                                   value="{{$depot->dlrm_ownm}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Owner Name" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Owner
                                            Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_olon" name="dlrm_olon"
                                                   class="form-control col-md-7 col-xs-12"
                                                   value="{{$depot->dlrm_olon}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Ln Owner Name" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile
                                            1
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_mob1" name="dlrm_mob1"
                                                   class="form-control col-md-7 col-xs-12"
                                                   value="{{$depot->dlrm_mob1}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Mobile 1" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile 2
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_mob2" name="dlrm_mob2"
                                                   class="form-control col-md-7 col-xs-12"
                                                   value="{{$depot->dlrm_mob2}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Mobile 2" type="text"
                                                   step="1">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_emal" name="dlrm_emal"
                                                   class="form-control col-md-7 col-xs-12"
                                                   value="{{$depot->dlrm_emal}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Email" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zip Code
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="dlrm_zpcd" name="dlrm_zpcd"
                                                   class="form-control col-md-7 col-xs-12"
                                                   value="{{$depot->dlrm_zpcd}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Zip Code" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" type="submit" class="btn btn-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            const slgp_id = '{{ $depot->slgp_id }}';
            const than_id = '{{ $depot->than_id }}';
            const base_id = '{{ $depot->base_id }}';
            if (slgp_id !== '') {
                $('#slgp_id').val(slgp_id);
            }
            if (than_id !== '') {
                $('#than_id').val(than_id);
            }
            if (base_id !== '') {
                $('#base_id').val(base_id);
            }
            $("#slgp_id").select2({width: 'resolve'});
            $("#than_id").select2({width: 'resolve'});
            $("#base_id").select2({width: 'resolve'});

        });
    </script>
@endsection